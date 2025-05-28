""" init file for views module """
from .login import LoginView
from .logout import LogoutView
from .user import UserView
from .kitdigital import KitDigitalCRUDView, KitDigitalStateView, KitDigitalListView

__all__ = [
  'LoginView',
  'LogoutView',
  'UserView',
  'KitDigitalCRUDView',
  'KitDigitalStateView',
  'KitDigitalListView',
]