# models.py
from django.db import models
import uuid
from django.contrib.auth.models import User
from typing import Self, Any

class AutonomousCommunity(models.Model):
  nombre = models.CharField(max_length=100)
  codigo = models.CharField(max_length=10, unique=True)
  
  class Meta:
    verbose_name = "Autonomous Community"
    verbose_name_plural = "Autonomous Communities"
  
  def __str__ (self: Self) -> str:
    return self.nombre

class Province(models.Model):
  community = models.ForeignKey(AutonomousCommunity, on_delete=models.CASCADE)
  nombre = models.CharField(max_length=100)
  codigo = models.CharField(max_length=10)
  
  class Meta:
    verbose_name = "Province"
    verbose_name_plural = "Provinces"
  
  def __str__ (self: Self) -> str:
    return f"{self.nombre} ({self.community})"

class Municipality(models.Model):
  province = models.ForeignKey(Province, on_delete=models.CASCADE)
  nombre = models.CharField(max_length=100)
  codigo = models.CharField(max_length=10)
  
  class Meta:
    verbose_name = "Municipality"
    verbose_name_plural = "Municipalities"
  
  def __str__ (self: Self) -> str:
    return f"{self.nombre} ({self.province})"

class DigitalKitType(models.Model):
  etiqueta = models.CharField(max_length=100)
  codigo = models.CharField(max_length=20, unique=True)
  activo = models.BooleanField(default=True)
  
  class Meta:
    verbose_name = "Digital Kit Type"
    verbose_name_plural = "Digital Kit Types"
  
  def __str__ (self: Self) -> str:
    return self.etiqueta

class Company(models.Model):
  TYPE_CHOICES = [
    ('fisica', 'Natural Person'),
    ('juridica', 'Legal Entity'),
  ]

  id = models.UUIDField(primary_key=True, default=uuid.uuid4, editable=False)
  tipo = models.CharField(max_length=10, choices=TYPE_CHOICES)
  nombre = models.CharField(max_length=255)
  nombre_comercial = models.CharField(max_length=255, blank=True, null=True)
  nif = models.CharField(max_length=20)
  telefono_fijo = models.CharField(max_length=20, blank=True, null=True)
  telefono_movil = models.CharField(max_length=20)
  direccion_completa = models.TextField(blank=True, null=True)
  codigo_postal = models.CharField(max_length=10, blank=True, null=True)
  notas = models.TextField(blank=True, null=True)

  # Location
  autonomous_community = models.ForeignKey(
    AutonomousCommunity, 
    on_delete=models.SET_NULL, 
    null=True, 
    blank=True
  )
  province = models.ForeignKey(
    Province, 
    on_delete=models.SET_NULL, 
    null=True, 
    blank=True
  )
  municipality = models.ForeignKey(
    Municipality, 
    on_delete=models.SET_NULL, 
    null=True, 
    blank=True
  )

  # Digital Kit
  applies_digital_kit = models.BooleanField(default=False)
  kit_type = models.ForeignKey(
    DigitalKitType, 
    on_delete=models.SET_NULL, 
    null=True, 
    blank=True
  )
  kit_status = models.ForeignKey(
    'DigitalKitStatus', 
    on_delete=models.SET_NULL, 
    null=True, 
    blank=True
  )
  kit_signed_pdf = models.BooleanField(default=False)
  kit_pdf_file = models.FileField(
    upload_to='digital_kit/pdfs/', 
    null=True, 
    blank=True
  )
  kit_approved_amount = models.DecimalField(
    max_digits=10, 
    decimal_places=2, 
    default=0
  )

  # Commissions
  kit_commission = models.DecimalField(
    max_digits=10, 
    decimal_places=2, 
    default=0
  )
  kit_commission_paid = models.DecimalField(
    max_digits=10, 
    decimal_places=2, 
    default=0
  )
  kit_invoice_issued = models.BooleanField(default=False)
  kit_invoice_date = models.DateField(null=True, blank=True)
  kit_invoice_paid = models.BooleanField(default=False)

  # Audit
  created_by = models.ForeignKey(
    User, 
    on_delete=models.SET_NULL, 
    null=True, 
    related_name='companies_created'
  )
  creation_date = models.DateTimeField(auto_now_add=True)
  update_date = models.DateTimeField(auto_now=True)

  class Meta:
    verbose_name = "Company"
    verbose_name_plural = "Companies"

  def __str__ (self: Self) -> str:
    return f"{self.nombre} ({self.nif})"

  def to_dict(self: Self) -> dict[Any|float, Any|None]:
    return {
      'id': str(self.id),
      'tipo': self.tipo,
      'nombre': self.nombre,
      'nombre_comercial': self.nombre_comercial,
      'nif': self.nif,
      'telefono_fijo': self.telefono_fijo,
      'telefono_movil': self.telefono_movil,
      'direccion_completa': self.direccion_completa,
      'codigo_postal': self.codigo_postal,
      'notas': self.notas,
      'autonomous_community': self.autonomous_community.id if self.autonomous_community else None,
      'province': self.province.id if self.province else None,
      'municipality': self.municipality.id if self.municipality else None,
      'applies_digital_kit': self.applies_digital_kit,
      'kit_type': self.kit_type.id if self.kit_type else None,
      'kit_status': self.kit_status.id if self.kit_status else None,
      'kit_signed_pdf': self.kit_signed_pdf,
      'kit_pdf_file': self.kit_pdf_file.url if self.kit_pdf_file else None,
      'kit_approved_amount': float(self.kit_approved_amount),
      'kit_commission': float(self.kit_commission),
      'kit_commission_paid': float(self.kit_commission_paid),
      'kit_invoice_issued': self.kit_invoice_issued,
      'kit_invoice_date': self.kit_invoice_date.isoformat() if self.kit_invoice_date else None,
      'kit_invoice_paid': self.kit_invoice_paid,
      'creation_date': self.creation_date.isoformat(),
      'update_date': self.update_date.isoformat()
    }