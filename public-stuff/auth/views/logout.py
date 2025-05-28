from django.contrib.auth import logout
from django.utils.decorators import method_decorator
from django.views.decorators.csrf import csrf_exempt
from django.views import View
from django.http import JsonResponse
from auth.models import UserToken
from typing import Self
from django.http import HttpRequest

@method_decorator(csrf_exempt, name='dispatch')
class LogoutView(View):
  def post(self: Self, request: HttpRequest) -> JsonResponse:
    # Obtener el token del header
    auth_header = request.META.get('HTTP_AUTHORIZATION', '')
    if auth_header.startswith('Bearer '):
      token = auth_header.split(' ')[1]
      
      # Invalidar el token
      try:
        user_token = UserToken.objects.get(token=token, is_active=True)
        user_token.is_active = False
        user_token.save()
      except UserToken.DoesNotExist:
        pass
    
    logout(request)
    return JsonResponse({'success': True})