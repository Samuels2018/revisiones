# views.py
from django.http import JsonResponse
from django.views import View
from django.core.paginator import Paginator
from django.db.models import Q
from auth.models import KitDigital, KitDigitalState
import json
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.http import HttpRequest
from typing import Self

@method_decorator(csrf_exempt, name='dispatch')
class KitDigitalListView(View):
  def get(self: Self, request: HttpRequest) -> JsonResponse:
    # Obtener parámetros de DataTables
    draw = int(request.GET.get('draw', 1))
    start = int(request.GET.get('start', 0))
    length = int(request.GET.get('length', 10))
    search_value = request.GET.get('search[value]', '')
    
    # Construir consulta base
    queryset = KitDigital.objects.all().order_by('-fecha_creacion')
    
    # Aplicar búsqueda global
    if search_value:
      queryset = queryset.filter(
        Q(nombre_completo__icontains=search_value) |
        Q(nif__icontains=search_value) |
        Q(telefono__icontains=search_value) |
        Q(poblacion__icontains=search_value)
      )
    
    # Aplicar búsqueda por columnas individuales
    for i in range(10):  # Asumiendo 10 columnas como en tu ejemplo
      col_search = request.GET.get(f'columns[{i}][search][value]', '')
      if col_search:
        if i == 0:  # ID
          queryset = queryset.filter(id__icontains=col_search)
        elif i == 1:  # Nombre Completo
          queryset = queryset.filter(nombre_completo__icontains=col_search)
        elif i == 2:  # Tipo
          queryset = queryset.filter(tipo=col_search)
        elif i == 3:  # NIF
          queryset = queryset.filter(nif__icontains=col_search)
        elif i == 4:  # Teléfono
          queryset = queryset.filter(telefono__icontains=col_search)
        elif i == 5:  # Población
          queryset = queryset.filter(poblacion__icontains=col_search)
        elif i == 6:  # Aplica KitDigital
          aplica = True if col_search == '1' else False
          queryset = queryset.filter(aplica_kit=aplica)
        elif i == 7:  # Estado KitDigital
          queryset = queryset.filter(estado__id=col_search)
    
    # Paginación
    paginator = Paginator(queryset, length)
    page = (start // length) + 1
    kits = paginator.get_page(page)
    
    # Preparar datos para respuesta
    data = []
    for kit in kits:
      data.append({
        'ID': str(kit.id),
        'Nombre Completo': kit.nombre_completo,
        'Tipo': kit.get_tipo_display(),
        'Cedula': kit.nif,
        'Telefono': kit.telefono,
        'Poblacion': kit.poblacion,
        'Aplica KitDigital': 'SI' if kit.aplica_kit else 'NO',
        'Estado KitDigital': kit.estado.etiqueta if kit.estado else '',
        'Monto': float(kit.monto),
        'Cobrado': float(kit.cobrado),
      })
    
    response = {
      'draw': draw,
      'recordsTotal': KitDigital.objects.count(),
      'recordsFiltered': queryset.count(),
      'data': data,
    }
    
    return JsonResponse(response)

@method_decorator(csrf_exempt, name='dispatch')
class KitDigitalStateView(View):
  def get(self: Self, request: HttpRequest) -> JsonResponse:
    estados = KitDigitalState.objects.filter(activo=True)
    data = [{
      'rowid': str(estado.id),
      'etiqueta': estado.etiqueta,
      'codigo': estado.codigo,
    } for estado in estados]
    return JsonResponse(data, safe=False)

@method_decorator(csrf_exempt, name='dispatch')
class KitDigitalCRUDView(View):
  def post(self: Self, request: HttpRequest) -> JsonResponse:
    try:
      data = json.loads(request.body)
      action = data.get('action')
      
      if action == 'crear':
        # Lógica para crear nuevo Kit Digital
        kit = KitDigital.objects.create(
          nombre_completo=data['nombre_completo'],
          tipo=data['tipo'],
          nif=data['nif'],
          telefono=data['telefono'],
          poblacion=data['poblacion'],
          aplica_kit=data.get('aplica_kit', False),
          estado_id=data.get('estado_id'),
          monto=data.get('monto', 0),
          cobrado=data.get('cobrado', 0),
        )
        return JsonResponse({'exito': 1, 'mensaje': 'Kit Digital creado exitosamente', 'id': str(kit.id)})
      
      elif action == 'actualizar':
        # Lógica para actualizar Kit Digital
        kit = KitDigital.objects.get(id=data['id'])
        kit.nombre_completo = data['nombre_completo']
        kit.tipo = data['tipo']
        kit.nif = data['nif']
        kit.telefono = data['telefono']
        kit.poblacion = data['poblacion']
        kit.aplica_kit = data.get('aplica_kit', False)
        kit.estado_id = data.get('estado_id')
        kit.monto = data.get('monto', 0)
        kit.cobrado = data.get('cobrado', 0)
        kit.save()
        return JsonResponse({'exito': 1, 'mensaje': 'Kit Digital actualizado exitosamente'})
      
      elif action == 'eliminar':
        # Lógica para eliminar Kit Digital
        KitDigital.objects.filter(id=data['id']).delete()
        return JsonResponse({'exito': 1, 'mensaje': 'Kit Digital eliminado exitosamente'})
      
      else:
        return JsonResponse({'exito': 0, 'mensaje': 'Acción no válida'}, status=400)
            
    except Exception as e:
      return JsonResponse({'exito': 0, 'mensaje': str(e)}, status=500)