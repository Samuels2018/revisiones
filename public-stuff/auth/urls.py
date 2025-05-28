# urls.py
from django.urls import path
from .views import (
  LoginView, 
  LogoutView, 
  UserView,
  KitDigitalCRUDView, 
  KitDigitalStateView, 
  KitDigitalListView,
  EmpresaAPIView,
  EmpresaKitDigitalAPIView,
  EmpresaComisionesAPIView,
  ComunidadesAutonomasAPIView,
  ProvinciasAPIView,
  MunicipiosAPIView,
  KitDigitalTiposAPIView,
  KitDigitalEstadosAPIView
)

urlpatterns = [
  path('login/', LoginView.as_view(), name='login'),
  path('logout/', LogoutView.as_view(), name='logout'),
  path('dashboard/', UserView.as_view(), name='user'),
  path('kit-digital/listado/', KitDigitalListView.as_view(), name='kit_digital_list'),
  path('kit-digital/estados/', KitDigitalStateView.as_view(), name='kit_digital_estados'),
  path('kit-digital/crud/', KitDigitalCRUDView.as_view(), name='kit_digital_crud'),
  path('api/empresas/', EmpresaAPIView.as_view(), name='empresa-list'),
  path('api/empresas/<uuid:empresa_id>/', EmpresaAPIView.as_view(), name='empresa-detail'),
  
  # Secciones específicas
  path('api/empresas/<uuid:empresa_id>/kit-digital/', EmpresaKitDigitalAPIView.as_view(), name='empresa-kit-digital'),
  path('api/empresas/<uuid:empresa_id>/comisiones/', EmpresaComisionesAPIView.as_view(), name='empresa-comisiones'),
  
  # Datos de referencia
  path('api/comunidades-autonomas/', ComunidadesAutonomasAPIView.as_view(), name='comunidades-list'),
  path('api/provincias/', ProvinciasAPIView.as_view(), name='provincias-list'),
  path('api/municipios/', MunicipiosAPIView.as_view(), name='municipios-list'),
  path('api/kit-digital/tipos/', KitDigitalTiposAPIView.as_view(), name='kit-digital-tipos'),
  path('api/kit-digital/estados/', KitDigitalEstadosAPIView.as_view(), name='kit-digital-estados'),
  path('api/empresas/<uuid:empresa_id>/pdf/', EmpresaPDFAPIView.as_view(), name='empresa-pdf'),
  #path('api/token/verify/', TokenVerifyView.as_view(), name='token_verify'),
]