from django.http import JsonResponse
from django.views import View
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.db import transaction
from auth.models import (
  AutonomousCommunity,
  Province,
  Municipality,
  DigitalKitType,
  Company,
  KitDigitalState
)
import json
import uuid
from django.http import HttpRequest
from typing import Self, Any

@method_decorator(csrf_exempt, name='dispatch')
class EmpresaAPIView(View):
  def get(self: Self, request: HttpRequest, empresa_id: str|Any = None) -> JsonResponse:
    if empresa_id:
      try:
        empresa = Company.objects.get(id=empresa_id)
        return JsonResponse({
          'success': True,
          'data': empresa.to_dict(),
          'tabs': {
            'datos': True,
            'kit_digital': empresa.aplica_kit_digital,
            'comisiones': True
          }
        })
      except Company.DoesNotExist:
        return JsonResponse({'success': False, 'error': 'Empresa no encontrada'}, status=404)
    else:
      empresas = Company.objects.all().order_by('-fecha_creacion')[:100]
      data = [empresa.to_dict() for empresa in empresas]
      return JsonResponse({'success': True, 'data': data})

  def post(self: Self, request: HttpRequest) -> JsonResponse:
    try:
      data = json.loads(request.body)
      
      with transaction.atomic():
        empresa = Company.objects.create(
          tipo=data.get('tipo'),
          nombre=data.get('nombre'),
          nombre_comercial=data.get('nombre_comercial'),
          nif=data.get('nif'),
          telefono_fijo=data.get('telefono_fijo'),
          telefono_movil=data.get('telefono_movil'),
          direccion_completa=data.get('direccion_completa'),
          codigo_postal=data.get('codigo_postal'),
          notas=data.get('notas'),
          comunidad_autonoma_id=data.get('comunidad_autonoma'),
          provincia_id=data.get('provincia'),
          municipio_id=data.get('municipio'),
          creado_por=request.user if request.user.is_authenticated else None
        )
        
        return JsonResponse({
          'success': True,
          'id': str(empresa.id),
          'message': 'Empresa creada exitosamente'
        })
            
    except Exception as e:
      return JsonResponse({
        'success': False,
        'error': str(e)
      }, status=400)

  def put(self: Self, request: HttpRequest, empresa_id: str|Any) -> JsonResponse:
    try:
      empresa = Company.objects.get(id=empresa_id)
      data = json.loads(request.body)
      
      with transaction.atomic():
        # Actualizar campos básicos
        empresa.tipo = data.get('tipo', empresa.tipo)
        empresa.nombre = data.get('nombre', empresa.nombre)
        empresa.nombre_comercial = data.get('nombre_comercial', empresa.nombre_comercial)
        empresa.nif = data.get('nif', empresa.nif)
        empresa.telefono_fijo = data.get('telefono_fijo', empresa.telefono_fijo)
        empresa.telefono_movil = data.get('telefono_movil', empresa.telefono_movil)
        empresa.direccion_completa = data.get('direccion_completa', empresa.direccion_completa)
        empresa.codigo_postal = data.get('codigo_postal', empresa.codigo_postal)
        empresa.notas = data.get('notas', empresa.notas)
        
        # Actualizar ubicación
        if 'comunidad_autonoma' in data:
          empresa.comunidad_autonoma_id = data['comunidad_autonoma']
        if 'provincia' in data:
          empresa.provincia_id = data['provincia']
        if 'municipio' in data:
          empresa.municipio_id = data['municipio']
        
        empresa.save()
        
        return JsonResponse({
          'success': True,
          'message': 'Empresa actualizada exitosamente'
        })
            
    except Company.DoesNotExist:
        return JsonResponse({'success': False, 'error': 'Empresa no encontrada'}, status=404)
    except Exception as e:
        return JsonResponse({
          'success': False,
          'error': str(e)
        }, status=400)

  def delete(self, request, empresa_id):
    try:
      empresa = Company.objects.get(id=empresa_id)
      empresa.delete()
      return JsonResponse({'success': True, 'message': 'Empresa eliminada exitosamente'})
    except Company.DoesNotExist:
      return JsonResponse({'success': False, 'error': 'Empresa no encontrada'}, status=404)

@method_decorator(csrf_exempt, name='dispatch')
class EmpresaKitDigitalAPIView(View):
  def put(self: Self, request: HttpRequest, empresa_id: str|Any) -> JsonResponse:
    try:
      empresa = Company.objects.get(id=empresa_id)
      data = json.loads(request.body)
      
      with transaction.atomic():
        # Actualizar campos de Kit Digital
        empresa.aplica_kit_digital = data.get('aplica_kit_digital', empresa.aplica_kit_digital)
        
        if 'kit_tipo' in data:
          empresa.kit_tipo_id = data['kit_tipo']
        if 'kit_estado' in data:
          empresa.kit_estado_id = data['kit_estado']
        
        empresa.kit_pdf_firmado = data.get('kit_pdf_firmado', empresa.kit_pdf_firmado)
        empresa.kit_monto_aprobado = data.get('kit_monto_aprobado', empresa.kit_monto_aprobado)
        
        empresa.save()
        
        return JsonResponse({
          'success': True,
          'message': 'Datos de Kit Digital actualizados exitosamente'
        })
            
    except Company.DoesNotExist:
      return JsonResponse({'success': False, 'error': 'Empresa no encontrada'}, status=404)
    except Exception as e:
      return JsonResponse({
        'success': False,
        'error': str(e)
      }, status=400)

@method_decorator(csrf_exempt, name='dispatch')
class EmpresaComisionesAPIView(View):
  def put(self: Self, request: HttpRequest, empresa_id: str|Any) -> JsonResponse:
    try:
      empresa = Company.objects.get(id=empresa_id)
      data = json.loads(request.body)
      
      with transaction.atomic():
        # Actualizar campos de comisiones
        empresa.kit_comision = data.get('kit_comision', empresa.kit_comision)
        empresa.kit_comision_pagada = data.get('kit_comision_pagada', empresa.kit_comision_pagada)
        empresa.kit_factura_emitida = data.get('kit_factura_emitida', empresa.kit_factura_emitida)
        
        if 'kit_factura_fecha' in data:
          empresa.kit_factura_fecha = data['kit_factura_fecha']
        
        empresa.kit_factura_pagada = data.get('kit_factura_pagada', empresa.kit_factura_pagada)
        
        empresa.save()
        
        return JsonResponse({
          'success': True,
          'message': 'Datos de comisiones actualizados exitosamente'
        })
            
    except Company.DoesNotExist:
      return JsonResponse({'success': False, 'error': 'Empresa no encontrada'}, status=404)
    except Exception as e:
      return JsonResponse({
        'success': False,
        'error': str(e)
      }, status=400)

class ComunidadesAutonomasAPIView(View):
  def get(self: Self, request: HttpRequest) -> JsonResponse:
    comunidades = AutonomousCommunity.objects.all().order_by('nombre')
    data = [{'id': c.id, 'nombre': c.nombre} for c in comunidades]
    return JsonResponse({'success': True, 'data': data})

class ProvinciasAPIView(View):
  def get(self: Self, request: HttpRequest) -> JsonResponse:
    comunidad_id = request.GET.get('comunidad_id')
    if not comunidad_id:
      return JsonResponse({'success': False, 'error': 'Se requiere comunidad_id'}, status=400)
        
    provincias = Province.objects.filter(comunidad_id=comunidad_id).order_by('nombre')
    data = [{'id': p.id, 'nombre': p.nombre} for p in provincias]
    return JsonResponse({'success': True, 'data': data})

class MunicipiosAPIView(View):
  def get(self: Self, request: HttpRequest) -> JsonResponse:
    provincia_id = request.GET.get('provincia_id')
    if not provincia_id:
      return JsonResponse({'success': False, 'error': 'Se requiere provincia_id'}, status=400)
        
    municipios = Municipality.objects.filter(provincia_id=provincia_id).order_by('nombre')
    data = [{'id': m.id, 'nombre': m.nombre} for m in municipios]
    return JsonResponse({'success': True, 'data': data})

class KitDigitalTiposAPIView(View):
  def get(self: Self, request: HttpRequest) -> JsonResponse:
    tipos = DigitalKitType.objects.filter(activo=True).order_by('etiqueta')
    data = [{'id': t.id, 'etiqueta': t.etiqueta} for t in tipos]
    return JsonResponse({'success': True, 'data': data})

class KitDigitalEstadosAPIView(View):
  def get(self: Self, request: HttpRequest) -> JsonResponse:
    estados = KitDigitalState.objects.filter(activo=True).order_by('etiqueta')
    data = [{'id': e.id, 'etiqueta': e.etiqueta} for e in estados]
    return JsonResponse({'success': True, 'data': data})
  

@method_decorator(csrf_exempt, name='dispatch')
class EmpresaPDFAPIView(View):
  def put(self: Self, request: HttpRequest, empresa_id: str|Any) -> JsonResponse:
    try:
      empresa = Company.objects.get(id=empresa_id)
      
      if 'kit_pdf_archivo' not in request.FILES:
        return JsonResponse({
          'success': False,
          'error': 'No se proporcionó archivo PDF'
        }, status=400)
          
      pdf_file = request.FILES['kit_pdf_archivo']
      
      # Validar que sea un PDF
      if not pdf_file.name.lower().endswith('.pdf'):
        return JsonResponse({
          'success': False,
          'error': 'El archivo debe ser un PDF'
        }, status=400)
          
      # Tamaño máximo 5MB
      if pdf_file.size > 5 * 1024 * 1024:
        return JsonResponse({
          'success': False,
          'error': 'El archivo no puede exceder 5MB'
        }, status=400)
          
      empresa.kit_pdf_archivo = pdf_file
      empresa.kit_pdf_firmado = True
      empresa.save()
      
      return JsonResponse({
        'success': True,
        'message': 'PDF subido exitosamente',
        'url': empresa.kit_pdf_archivo.url
      })
        
    except Company.DoesNotExist:
      return JsonResponse({'success': False, 'error': 'Empresa no encontrada'}, status=404)
    except Exception as e:
      return JsonResponse({
        'success': False,
        'error': str(e)
    }, status=400)