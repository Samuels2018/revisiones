# auth/decorators.py
import jwt
from django.conf import settings
from django.http import JsonResponse
from functools import wraps
from auth.models import UserToken

def jwt_required(view_func):
  @wraps(view_func)
  def wrapped_view(request, *args, **kwargs):
    auth_header = request.headers.get('Authorization')
    
    if not auth_header or not auth_header.startswith('Bearer '):
      return JsonResponse({'error': 'Token no proporcionado'}, status=401)
        
    token = auth_header.split(' ')[1]
    
    try:
      # Verificar token en la base de datos
      user_token = UserToken.objects.get(token=token, is_active=True)
        
      if user_token.is_expired:
        return JsonResponse({'error': 'Token expirado'}, status=401)
            
      # Verificar token JWT
      payload = jwt.decode(token, settings.JWT_SECRET_KEY, algorithms=['HS256'])
      request.user_id = payload['user_id']
        
    except UserToken.DoesNotExist:
      return JsonResponse({'error': 'Token inválido'}, status=401)
    except jwt.ExpiredSignatureError:
      return JsonResponse({'error': 'Token expirado'}, status=401)
    except jwt.InvalidTokenError:
      return JsonResponse({'error': 'Token inválido'}, status=401)
        
    return view_func(request, *args, **kwargs)
  return wrapped_view