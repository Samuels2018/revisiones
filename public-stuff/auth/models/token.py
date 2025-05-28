from django.db import models
from datetime import datetime, timedelta
from .user import User
import os
from typing import cast, Self, Any

class UserToken (models.Model):
    """Modelo personalizado para almacenar tokens JWT"""
    user = models.ForeignKey(
      User,
      on_delete=models.CASCADE,
      related_name='tokens'
    )
    token = models.CharField(max_length=500, unique=True)
    created_at = models.DateTimeField(auto_now_add=True)
    expires_at = models.DateTimeField()
    is_active = models.BooleanField(default=True)
    
    class Meta:
      db_table = 'usuario_tokens'
      ordering = ['-created_at']
    
    def __str__(self: Self) -> str:
      return f"Token para {self.user.acceso_usuario} (expira: {self.expires_at})"
    
    def save(self: Self, *args: Any, **kwargs: Any) -> None:
      if not self.pk:  # Solo en creación
        self.expires_at = datetime.now() + timedelta(days=cast(int, os.getenv('JWT_EXPIRATION_DAYS', 7)))
      super().save(*args, **kwargs)
    
    @property
    def is_expired(self: Self) -> str:
      return datetime.now() > self.expires_at