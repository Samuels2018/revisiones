from django.http import JsonResponse
from django.contrib.auth import authenticate
from django.views.decorators.csrf import csrf_exempt
from django.utils.decorators import method_decorator
from django.views import View
import json
from datetime import datetime
from django.http import HttpRequest
from typing import Self

@method_decorator(csrf_exempt, name='dispatch')
class LoginView(View):
  def post(self: Self, request: HttpRequest) -> JsonResponse:
    try:
      data = json.loads(request.body)
      email = data.get('email')
      password = data.get('password')
      
      user = authenticate(request, username=email, password=password)
      
      if user is None:
        return JsonResponse(
          {'error': 'Email o contraseña incorrectos'}, 
          status=401
        )
      
      if not user.is_active or (user.fk_estado and not user.fk_estado.logeable):
        return JsonResponse(
          {'error': 'Tu cuenta no está habilitada para iniciar sesión'}, 
          status=403
        )
      
      # Generar token JWT
      token = user.generate_jwt_token()
      
      # Actualizar último login
      user.ultimo_login = datetime.now()
      user.save()
      
      return JsonResponse({
        'token': token,
        'user': {
          'id': str(user.id),
          'email': user.acceso_usuario,
          'nombre': user.nombre,
          'avatar': user.avatar_url,
        }
      })
    
    except Exception as e:
      return JsonResponse(
        {'error': 'Error en el servidor: ' + str(e)}, 
        status=500
      )
