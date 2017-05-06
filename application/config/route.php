<?php

return $routes = array(
	/*new TamtayRoute('xem-film-theo-danh-muc/:catid-:duration/+cat_name','category','view'),
	new TamtayRoute('xem-film-theo-tieu-chi/:type-:duration/+type_name','category','filter'),
	new TamtayRoute('gioi-thieu-film','cinema','index'),
	new TamtayRoute('xem-gioi-thieu-fim/:pid-:partit/+info_film','phim','view'),
	new TamtayRoute('xem-fim-truc-tuyen/:pid-:partit/+info_film','phim','watch'),
	new TamtayRoute('xem-film-theo-dien-vien/:aid/+actor_name','category','actor'),
	new TamtayRoute('xem-film-theo-tag/:aid/+actor_name','category','tag'),*/
    new FrameworkRoute('dang-nhap','user','login'),
	new FrameworkRoute('trang-chu','home','index'),
	new FrameworkRoute('le-tan','receptionist','index'),
	new FrameworkRoute('phong-kham','receptionist','view'),
	new FrameworkRoute('danh-sach','receptionist','list'),
	new FrameworkRoute('invoice-service','home','service'),
);