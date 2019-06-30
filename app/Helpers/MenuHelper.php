<?php

namespace App\Helpers;

use App\Models\Sistema\Menu as MenuElemento;
use Route;
use Auth;
use Session;

class MenuHelper {

	protected $menus;

	protected $menusOriginal;

	protected $path;

	protected $prefix;

	public function __construct()
	{
		$this->menus = collect([]);

		$menusDelPerfil = array();
		if(!Auth::guest() && Session::has('entidad'))
		{
			foreach(Auth::user()->perfiles as $perfil)
			{
				if($perfil->entidad->id == Session::get('entidad')->id)
				{
					$menusDelPerfil = $perfil->menus;
					break;
				}
			}
		}

		$this->menusOriginal = $this->limpiarMenus($menusDelPerfil);

		$menusDelPerfil = $this->limpiarMenus($menusDelPerfil);

		$this->setMenus(null, $menusDelPerfil, $this->menus);

		$this->setPath();

		$this->setPrefix();

		$this->setActivo($this->menus);
	}

	protected function limpiarMenus($menus)
	{
		$menuRetorno = collect([]);
		foreach($menus as $menu){
			$elemento = (object)array(
					'id' => $menu->id,
					'menu_padre_id' => $menu->menu_padre_id,
					'orden' => $menu->orden,
					'ruta' => $menu->ruta,
					'nombre' => $menu->nombre,
					'pre_icon' => $menu->pre_icon,
					'activo' => false
				);
			$menuRetorno->push($elemento);
		}
		return $menuRetorno;
	}

	protected function setMenus($padre, &$menus, &$coleccionMenus)
	{
		foreach($menus as $key => $menu)
		{
			if($menu->menu_padre_id == $padre)
			{
				$elemento = $menus->pull($key);
				$elemento->hijos = collect([]);
				$this->setMenus($elemento->id, $menus, $elemento->hijos);
				$coleccionMenus->push($elemento);
			}
		}
	}

	public function setPrefix()
	{
		$prefix = Route::current()->getPrefix();

		$this->prefix = $prefix;
	}

	public function getPrefix()
	{
		return $this->prefix;
	}

	public function setPath()
	{
		$path = Route::current()->uri();
		$path = explode('/', $path);

		$this->path = $path[0];
	}

	public function getPath($single = true)
	{
		if($single)
		{
			return $this->path;
		}
		else
		{
			return Route::current()->uri();
		}

	}

	public function menus()
	{
		return $this->menus;
	}

	protected function setActivo(&$menus)
	{
		foreach ($menus as &$menu)
		{
			if($menu->ruta == $this->getPath() || $menu->ruta == $this->getPath(false))
			{
				$menu->activo = true;
				break;
			}
			if(count($menu->hijos) > 0)
			{
				$this->setActivo($menu->hijos);
				foreach ($menu->hijos as $key)
				{
					if($key->activo)
					{
						$menu->activo = true;
						break;
					}
				}
			}
		}
	}

	public function menuEsPermitido()
	{
		foreach ($this->menusOriginal as $menu)
		{
			if($menu->ruta == $this->getPath() || $menu->ruta == $this->getPath(false))
			{
				return true;
				break;
			}
		}
		return false;
	}

}