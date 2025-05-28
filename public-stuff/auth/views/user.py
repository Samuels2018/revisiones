
from django.http import JsonResponse
from django.contrib.auth import authenticate
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
from auth.models import UserToken
from typing import Self
from django.http import HttpRequest


@method_decorator(csrf_exempt, name='dispatch')
class UserView(View):
  def get(self: Self, request: HttpRequest) -> JsonResponse:
    # Verificar autenticación
    auth_header = request.META.get('HTTP_AUTHORIZATION', '')
    if not auth_header.startswith('Bearer '):
      return JsonResponse(
        {'error': 'No autorizado'}, 
        status=401 #status.HTTP_401_UNAUTHORIZED
      )
    
    token = auth_header.split(' ')[1]
    
    try:
      # Verificar token
      user_token = UserToken.objects.get(token=token, is_active=True)
      if user_token.is_expired:
        return JsonResponse(
          {'error': 'Token expirado'}, 
          status=401 #status.HTTP_401_UNAUTHORIZED
        )
      
      # Obtener usuario
      user = user_token.user
      user_data = {
        'id': str(user.id),
        'acceso_usuario': user.acceso_usuario,
        'nombre': user.nombre,
        'avatar_url': user.avatar_url,
        'fecha_creacion': user.fecha_creacion.isoformat(),
        'ultimo_login': user.ultimo_login.isoformat() if user.ultimo_login else None,
        'is_active': user.is_active,
        'is_staff': user.is_staff,
        'is_superuser': user.is_superuser,
      }
      
      if user.fk_estado:
        user_data['fk_estado'] = {
          'id': user.fk_estado.id,
          'logeable': user.fk_estado.logeable,
          'etiqueta': user.fk_estado.etiqueta,
          'codigo': user.fk_estado.codigo
        }
      
      return JsonResponse(user_data)
        
    except UserToken.DoesNotExist:
      return JsonResponse(
        {'error': 'Token inválido'},
        status=401 #status.HTTP_401_UNAUTHORIZED
      )

"""@method_decorator(csrf_exempt, name='dispatch')
class ValidateSessionView(APIView):
    def get(self, request):
        # Similar a UserView pero solo verifica validez
        auth_header = request.META.get('HTTP_AUTHORIZATION', '')
        if not auth_header.startswith('Bearer '):
            return JsonResponse(
                {'valid': False, 'message': 'No autorizado'}, 
                status=status.HTTP_401_UNAUTHORIZED
            )
        
        token = auth_header.split(' ')[1]
        
        try:
            user_token = UserToken.objects.get(token=token, is_active=True)
            if user_token.is_expired:
                return JsonResponse(
                    {'valid': False, 'message': 'Token expirado'}, 
                    status=status.HTTP_401_UNAUTHORIZED
                )
            
            return JsonResponse({
                'valid': True,
                'message': 'Sesión válida'
            })
            
        except UserToken.DoesNotExist:
            return JsonResponse(
                {'valid': False, 'message': 'Token inválido'}, 
                status=status.HTTP_401_UNAUTHORIZED
            )

"""