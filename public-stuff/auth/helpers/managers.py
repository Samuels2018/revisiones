from django.contrib.auth.models import BaseUserManager
from auth.models import UserState


class UserManager (BaseUserManager):
  def create_user(self, acceso_usuario, acceso_clave=None, **extra_fields):
    """
    Crea y guarda un usuario con el email y contraseña dados.
    """
    if not acceso_usuario:
      raise ValueError('El usuario debe tener un email')
    
    acceso_usuario = self.normalize_email(acceso_usuario)
    
    user = self.model(
      acceso_usuario=acceso_usuario,
      **extra_fields
    )
    
    user.set_password(acceso_clave)
    user.save(using=self._db)
    return user
  
  def create_superuser(self, acceso_usuario, acceso_clave, **extra_fields):
    """
    Crea y guarda un superusuario con el email y contraseña dados.
    """
    extra_fields.setdefault('is_staff', True)
    extra_fields.setdefault('is_superuser', True)
    extra_fields.setdefault('fk_estado', UserState.objects.get_or_create(
      codigo='activo',
      defaults={
        'logeable': True,
        'etiqueta': 'Usuario activo',
      }
    )[0])

    if extra_fields.get('is_staff') is not True:
      raise ValueError('Superuser debe tener is_staff=True.')
    if extra_fields.get('is_superuser') is not True:
      raise ValueError('Superuser debe tener is_superuser=True.')

    return self.create_user(acceso_usuario, acceso_clave, **extra_fields)