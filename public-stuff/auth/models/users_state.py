from django.db import models
from typing import Any, Self

class UserState (models.Model):
  """Estado de los usuarios con opciones de login"""
  logeable = models.BooleanField(default=False, verbose_name='¿Puede iniciar sesión?')
  etiqueta = models.CharField(max_length=255, verbose_name='Descripción del estado')
  codigo = models.CharField(max_length=50, unique=True, verbose_name='Código identificador')
  
  class Meta:
    db_table = 'diccionario_usuarios_estado'
    verbose_name = 'Estado de usuario'
    verbose_name_plural = 'Estados de usuario'
    ordering = ['id']
  
  def __str__(self: Self) -> str:
    return f"{self.etiqueta} ({'Activo' if self.logeable else 'Inactivo'})"
  
  def to_dict(self: Self) -> dict[str, Any]:
    """Devuelve una representación en diccionario del estado del usuario."""
    return {
      'logeable': self.logeable,
      'etiqueta': self.etiqueta,
      'codigo': self.codigo,
    }