from django.db import models
import uuid
from django.contrib.auth.models import AbstractBaseUser, PermissionsMixin
from datetime import datetime, timedelta
from .users_state import UserState
from .token import UserToken
from auth.helpers import UserManager
from typing import cast, Self
import os
import jwt

class User (AbstractBaseUser, PermissionsMixin):
  """Modelo de usuario personalizado"""
  id = models.UUIDField(primary_key=True, default=uuid.uuid4, editable=False)
  acceso_usuario = models.EmailField(
    unique=True, 
    verbose_name='Email',
    error_messages={
      'unique': 'Ya existe un usuario con este email.',
    }
  )
  nombre = models.CharField(max_length=255, verbose_name='Nombre completo')
  usuario_avatar = models.ImageField(
    upload_to='avatars/', 
    null=True, 
    blank=True,
    verbose_name='Avatar'
  )
  fk_estado = models.ForeignKey(
    UserState, 
    on_delete=models.SET_NULL, 
    null=True, 
    blank=True,
    verbose_name='Estado del usuario'
  )
  fecha_creacion = models.DateTimeField(auto_now_add=True, verbose_name='Fecha de creación')
  ultimo_login = models.DateTimeField(null=True, blank=True, verbose_name='Último acceso')
  
  # Campos para autenticación
  is_active = models.BooleanField(default=True)
  is_staff = models.BooleanField(default=False)
  is_superuser = models.BooleanField(default=False)
  
  objects = UserManager()
  
  USERNAME_FIELD = 'acceso_usuario'
  REQUIRED_FIELDS = ['nombre']
  
  class Meta:
    db_table = 'usuarios'
    verbose_name = 'Usuario'
    verbose_name_plural = 'Usuarios'
    ordering = ['-fecha_creacion']
  
  def __str__(self: Self) -> str:
    return f"{self.nombre} ({self.acceso_usuario})"
  
  def get_full_name(self: Self) -> str:
    return self.nombre
  
  def get_short_name(self: Self) -> str:
    return self.nombre.split()[0] if self.nombre else self.acceso_usuario
  
  def generate_jwt_token(self: Self) -> str:
    """Genera un token JWT para el usuario"""
    payload = {
      'user_id': str(self.id),
      'email': self.acceso_usuario,
      'exp': datetime.utcnow() + timedelta(days=cast(int, os.getenv('JWT_EXPIRATION_DAYS', 7))),
      'iat': datetime.utcnow()
    }
    
    token = jwt.encode(payload, os.getenv('JWT_SECRET_KEY', '') , algorithm='HS256')
    
    # Guardar el token en la base de datos
    UserToken.objects.create(
      user=self,
      token=token,
      expires_at=datetime.now() + timedelta(days=cast(int, os.getenv('JWT_EXPIRATION_DAYS', 7)))
    )
    
    return token
  
  @property
  def avatar_url(self: Self) -> str:
    """URL del avatar del usuario"""
    if self.usuario_avatar and hasattr(self.usuario_avatar, 'url'):
      return self.usuario_avatar.url
    return f'https://ui-avatars.com/api/?name={self.get_short_name()}&background=random'