# models.py
from django.db import models
import uuid
from typing import Self

class KitDigitalState (models.Model):
  """Estados para el Kit Digital"""
  etiqueta = models.CharField(max_length=255, verbose_name='Descripción del estado')
  codigo = models.CharField(max_length=50, unique=True, verbose_name='Código identificador')
  activo = models.BooleanField(default=True, verbose_name='¿Estado activo?')
  
  class Meta:
    db_table = 'kit_digital_estados'
    verbose_name = 'Estado Kit Digital'
    verbose_name_plural = 'Estados Kit Digital'
  
  def __str__(self: Self) -> str:
    return f"{self.etiqueta} ({'Activo' if self.activo else 'Inactivo'})"

class KitDigital (models.Model):
  TIPO_CHOICES = [
    ('fisica', 'Persona Física'),
    ('juridica', 'Persona Jurídica'),
  ]
  
  id = models.UUIDField(primary_key=True, default=uuid.uuid4, editable=False)
  nombre_completo = models.CharField(max_length=255, verbose_name='Nombre completo')
  tipo = models.CharField(max_length=10, choices=TIPO_CHOICES, verbose_name='Tipo')
  nif = models.CharField(max_length=20, verbose_name='NIF/Cédula')
  telefono = models.CharField(max_length=20, verbose_name='Teléfono')
  poblacion = models.CharField(max_length=100, verbose_name='Población')
  aplica_kit = models.BooleanField(default=False, verbose_name='Aplica Kit Digital')
  estado = models.ForeignKey(
    KitDigitalState , 
    on_delete=models.SET_NULL, 
    null=True, 
    blank=True,
    verbose_name='Estado Kit Digital'
  )
  monto = models.DecimalField(max_digits=10, decimal_places=2, verbose_name='Monto')
  cobrado = models.DecimalField(max_digits=10, decimal_places=2, verbose_name='Cobrado')
  fecha_creacion = models.DateTimeField(auto_now_add=True, verbose_name='Fecha de creación')
  
  class Meta:
    db_table = 'kit_digital'
    verbose_name = 'Kit Digital'
    verbose_name_plural = 'Kits Digitales'
    ordering = ['-fecha_creacion']
  
  def __str__(self: Self) -> str:
    return f"{self.nombre_completo} ({self.nif})"