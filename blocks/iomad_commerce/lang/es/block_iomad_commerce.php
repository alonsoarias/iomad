<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['add_course_to_shop'] = 'Añadir nuevo producto';
$string['add_more_license_blocks'] = 'Añadir otro bloque de licencias';
$string['addnewcourse'] = 'Añadir nuevo producto';
$string['allow_license_blocks' ] = 'Permitir bloques de licencias';
$string['allow_license_blocks_help'] = 'Cuando está habilitado, un administrador cliente puede comprar bloques de cursos';
$string['allow_single_purchase'] = 'Permitir compra individual';
$string['allow_single_purchase_help'] = 'Cuando la compra individual está habilitada, los usuarios individuales pueden comprar su propio acceso al curso';
$string['amount'] = 'Cantidad';
$string['blocktitle'] = 'IOMAD Comercio Electrónico';
$string['basket'] = 'Cesta';
$string['basket_1item'] = 'Hay {$a} artículo en su cesta.';
$string['basket_nitems'] = 'Hay {$a} artículos en su cesta.';
$string['buycourses'] = 'Comprar cursos';
$string['buynow'] = 'Comprar ahora';
$string['categorization'] = 'Categorización';
$string['checkout'] = 'Finalizar compra';
$string['checkoutpreamble'] = '<p></p>';
$string['commerce_admin_email'] = 'Dirección de correo del administrador de comercio';
$string['commerce_admin_email_help'] = 'Dirección de correo electrónico de la persona que se encarga de la tienda.';
$string['commerce_enabled'] = '{$a} habilitado';
$string['commerce_admin_firstname'] = 'Nombre mostrado del administrador de comercio';
$string['commerce_admin_firstname_help'] = 'Nombre de la persona que se encarga de la tienda. Usado en correos electrónicos';
$string['commerce_admin_lastname'] = 'Apellido mostrado del administrador de comercio';
$string['commerce_admin_lastname_help'] = 'Apellido de la persona que se encarga de la tienda. Usado en correos electrónicos';
$string['commerce_default_license_access_length'] = 'Días de acceso predeterminados de la licencia';
$string['commerce_default_license_access_length_help'] = 'Este es el período de tiempo en días que los usuarios tienen acceso a los cursos cuando se compran desde la tienda externa.';
$string['commerce_admin_default_license_shelf_life'] = 'Días de vida útil predeterminados de la licencia';
$string['commerce_admin_default_license_shelf_life_help'] = 'Este es el período de tiempo en días que la licencia puede permanecer sin usar antes de que ya no sea válida.';
$string['commerce_externalshop_link_timeout'] = 'Número de segundos para que expire el token de la tienda externa';
$string['commerce_externalshop_link_timeout_help'] = 'Este es el número de segundos que el enlace a la tienda externa seguirá siendo válido. Tener esto en un número grande significará que los enlaces podrían ser robados para que otro usuario inicie sesión en la tienda.';
$string['commerce_externalshop_url'] = 'URL predeterminada para el sitio de comercio electrónico externo';
$string['commerce_externalshop_url_company'] = 'URL para el sitio de comercio electrónico externo específico de la empresa';
$string['confirm'] = 'Confirmar';
$string['confirmation'] = 'Su pedido está completo';
$string['Course'] = 'Producto';
$string['courses'] = 'Productos';
$string['course_list_title'] = 'Gestionar productos de comercio electrónico';
$string['course_list_title_default'] = 'Gestionar plantillas de productos de comercio electrónico';
$string['course_long_description'] = 'Descripción larga';
$string['course_shop_enabled'] = 'Visible en la tienda';
$string['course_shop_enabled_help'] = 'Configure como <b>Sí</b> para que este curso se muestre en la pantalla de la tienda';
$string['course_shop_title'] = 'Tienda';
$string['course_short_summary'] = 'Descripción breve';
$string['coursedeletecheckfull'] = '¿Está absolutamente seguro de que desea eliminar {$a} y su configuración de la tienda?';
$string['courseunavailable'] = 'Este producto no está disponible.';
$string['courseshoptagcreated'] = 'Etiqueta de tienda de curso creada';
$string['courseshoptagdeleted'] = 'Etiqueta de tienda de curso eliminada';
$string['currency'] = 'Moneda';
$string['decimalnumberonly'] = 'Solo números decimales, por favor.';
$string['deletecourse'] = 'Eliminar producto de la tienda';
$string['deleteshoptag'] = 'Eliminar etiqueta de tienda - {$a}';
$string['deleteshoptagcheck'] = '¿Está seguro de que desea eliminar esta etiqueta de tienda?';
$string['deleteshoptagcheckused'] = 'Está siendo utilizada por los artículos de la tienda: {$a}. ¿Está seguro de que desea eliminar esta etiqueta de tienda?';
$string['edit_course_shopsettings'] = 'Editar configuración de la tienda del producto';
$string['edit_invoice'] = 'Editar pedido';
$string['exportproduct'] = 'Exportar artículo de producto';
$string['exportproductcheckfull'] = 'Esto guardará el artículo de producto seleccionado como una plantilla que luego puede asignarse a otras empresas.';
$string['importproduct'] = 'Importar artículo de producto';
$string['importproductcheckfull'] = 'Esto creará un nuevo artículo de producto en la tienda de la empresa actual desde esta plantilla. Cualquier curso que la empresa actual no pueda ver se eliminará del producto, así que verifique la configuración del producto después de la importación.';
$string['iomad_commerce:add_course'] = 'Añadir un producto a la tienda';
$string['iomad_commerce:addinstance'] = 'Añadir un nuevo bloque de comercio electrónico IOMAD';
$string['iomad_commerce:admin_view'] = 'Ver las páginas de administración de comercio electrónico';
$string['iomad_commerce:buyinbulk'] = 'Acceso para comprar artículos al por mayor en la tienda';
$string['iomad_commerce:buyitnow'] = 'Acceso al botón \'comprar ahora\' en la tienda';
$string['iomad_commerce:delete_course'] = 'Eliminar un producto de la tienda';
$string['iomad_commerce:edit_course'] = 'Editar un producto en la tienda';
$string['iomad_commerce:hide_course'] = 'Ocultar un producto en la tienda';
$string['iomad_commerce:manage_default'] = 'Gestionar plantillas de productos';
$string['iomad_commerce:manage_tags'] = 'Gestionar etiquetas';
$string['iomad_commerce:myaddinstance'] = 'Añadir un nuevo bloque de comercio electrónico IOMAD al panel del usuario';
$string['itemaddedsuccessfully'] = 'Producto creado correctamente';
$string['itemsusedby'] = 'Artículos que usan la etiqueta';
$string['emptybasket'] = 'Su cesta está vacía';
$string['error_duplicateblockstarts'] = '# de licencias duplicado.';
$string['error_incompatibletype'] = 'La compra individual solo se puede permitir para múltiples cursos cuando se trata de un programa de cursos';
$string['error_invalidblockstarts'] = 'Uno o más # de licencias no es válido.';
$string['error_invalidblockprices'] = 'Uno o más precios no son válidos.';
$string['error_invalidblockvalidlengths'] = 'Una o más longitudes válidas no son válidas.';
$string['error_invalidblockshelflives'] = 'Una o más vidas útiles no son válidas.';
$string['error_invalidlicenseamount'] = 'La cantidad de licencias debe ser 1 o más';
$string['error_invalidlicenseprice'] = 'Se requiere precio';
$string['error_invalidlicensenumber'] = 'El valor para el primer registro de # de licencias debe ser 1 o 2';
$string['error_singlepurchaseprice'] = 'El precio de compra individual debe ser mayor que 0.';
$string['error_singlepurchasevalidlength'] = 'La longitud válida debe ser mayor que 0.';
$string['error_singlepurchaseunavailable'] = 'Una compra de licencia individual no está disponible para este producto';
$string['filter_by_tag'] = 'Elegir productos por categoría: ';
$string['filtered_by_tag'] = 'Productos dentro de la categoría - {$a}.';
$string['filtered_by_search'] = 'Buscó {$a}.';
$string['gotoshop'] = 'Ir a la tienda';
$string['hide'] = 'Ocultar de la tienda';
$string['learning_paths'] = 'Rutas de Aprendizaje';
$string['licenseblock_start'] = 'Desde # de licencias';
$string['licenseblock_price'] = 'Precio por licencia';
$string['licenseblock_shelflife'] = 'Vida útil (días)';
$string['licenseblock_validlength'] = 'Válida (días)';
$string['licenseblock_n'] = '{$a} o más licencias';
$string['licenseblocks'] = 'Bloques de licencias';
$string['licenseformempty'] = 'Por favor, ingrese el número de licencias que necesita.';
$string['licenseoptionsavailableforregisteredcompanies'] = 'Opciones de licencia disponibles para empresas registradas, por favor inicie sesión';
$string['loginforlicenseoptions'] = 'Por favor inicie sesión para ver las opciones de licencia';
$string['managecompanyproducts'] = 'Gestionar productos de la empresa';
$string['managedefaultproducts'] = 'Gestionar plantillas de productos';
$string['managetags'] = 'Gestionar etiquetas';
$string['managetagsviewed'] = 'Gestionar etiquetas vistas';
$string['missingshortsummary'] = 'Falta la descripción breve.';
$string['moreinfo'] = 'más información';
$string['multiplecurrencies'] = 'ADVERTENCIA: Ha añadido artículos de la tienda que tienen diferentes monedas. No puede completar su transacción.';
$string['name'] = 'Nombre';
$string['nocoursesnotontheshop'] = 'No hay cursos disponibles para añadir a la tienda.';
$string['nocoursesontheshop'] = 'No hay productos en la tienda que coincidan con sus criterios.';
$string['notagsexist'] = 'No hay etiquetas disponibles';
$string['noinvoices'] = 'No hay facturas que coincidan con sus criterios.';
$string['noproviders'] = 'No se han habilitado proveedores de pago. Por favor, contacte al administrador del sitio';
$string['notconfigured'] = 'El bloque de comercio electrónico no ha sido configurado. Revise la página de configuración del bloque';
$string['opentoallcompanies'] = 'La tienda está disponible para todas las empresas';
$string['opentoallcompanies_help'] = 'Si esto está deshabilitado, entonces el acceso a la tienda se puede activar por empresa a través del panel de control IOMAD. Si lo habilita por primera vez, entonces necesitará activar la tienda para las empresas que lo requieran.';
$string['or'] = 'o';
$string['order'] = 'Pedido';
$string['orders'] = 'Pedidos';
$string['onlyonecoursepath'] = 'No puede seleccionar cursos y rutas de aprendizaje';
$string['payment_options'] = 'Opciones de pago';
$string['paymentprocessing'] = 'Procesamiento de pago';
$string['paymentprovider'] = 'Proveedor de pago';
$string['payment_provider_disabled'] = '{$a} está deshabilitado';
$string['paymentprovider_enabled'] = 'Habilitar {$a}';
$string['paymentprovider_enabled_help'] = 'Seleccione esto si desea que {$a} esté habilitado como proveedor de pago.';
$string['pluginname'] = 'IOMAD Comercio Electrónico';
$string['postcode'] = 'Código postal';
$string['pp_historic'] = 'PayPal histórico';
$string['pricefrom'] = 'Desde {$a}';
$string['priceoptions'] = 'Opciones de precio';
$string['privacy:metadata'] = 'El bloque de comercio electrónico IOMAD almacena datos personales de las facturas creadas.';
$string['privacy:metadata:invoice:id'] = 'ID de la tabla {invoice}';
$string['privacy:metadata:invoice:reference'] = 'Referencia de la factura';
$string['privacy:metadata:invoice:userid'] = 'Id de usuario de la factura';
$string['privacy:metadata:invoice:status'] = 'Estado de la factura';
$string['privacy:metadata:invoice:checkout_method'] = 'Método de pago de la factura';
$string['privacy:metadata:invoice:email'] = 'Dirección de correo electrónico de la factura';
$string['privacy:metadata:invoice:phone1'] = 'Número de teléfono principal de la factura';
$string['privacy:metadata:invoice:paymentid'] = 'Id de pago de Moodle de la factura';
$string['privacy:metadata:invoice:company'] = 'Empresa de la factura';
$string['privacy:metadata:invoice:address'] = 'Dirección de la factura';
$string['privacy:metadata:invoice:city'] = 'Ciudad de la factura';
$string['privacy:metadata:invoice:state'] = 'Estado/Provincia de la factura';
$string['privacy:metadata:invoice:country'] = 'País de la factura';
$string['privacy:metadata:invoice:postcode'] = 'Código postal de la factura';
$string['privacy:metadata:invoice:firstname'] = 'Nombre de la factura';
$string['privacy:metadata:invoice:lastname'] = 'Apellido de la factura';
$string['privacy:metadata:invoice:date'] = 'Fecha de pago de la factura';
$string['privacy:metadata:invoice'] = 'Metadatos de la factura';
$string['process'] = 'Procesar';
$string['processed'] = 'Procesado';
$string['process_help'] = 'Los artículos con casillas marcadas en la columna \'Procesar\' se procesarán como \'pedido completo\' al guardar los cambios.';
$string['productexportedsuccessfully'] = 'El artículo de producto se guardó correctamente como plantilla.';
$string['productexportfailed'] = 'Hubo un problema al intentar guardar este artículo de producto como plantilla. Si esto persiste, contacte al administrador del sitio.';
$string['productimportedsuccessfully'] = 'El artículo de plantilla se guardó correctamente como producto.';
$string['productimportfailed'] = 'Hubo un problema al intentar guardar este artículo de plantilla como producto. Si esto persiste, contacte al administrador del sitio.';
$string['product_login'] = "para comprar este producto";
$string['purchaser_details'] = 'Comprador';
$string['reference'] = 'Referencia';
$string['remove_filter'] = 'Mostrar todos los productos';
$string['returntoshop'] = 'Continuar comprando';
$string['review'] = 'Revisar su pedido';
$string['requiredcoursepath'] = 'Necesita seleccionar un curso o ruta de aprendizaje';
$string['search'] = 'Buscar';
$string['selectcoursetoadd'] = 'Seleccionar producto para añadir a la tienda';
$string['select_tag'] = 'Seleccionar categoría';
$string['shop'] = 'Tienda';
$string['shop_login_title'] = 'Por favor inicie sesión para acceder a la tienda';
$string['shop_title'] = 'Tienda';
$string['shoptagdeleted'] = 'Etiqueta de tienda eliminada';
$string['show'] = 'Mostrar en la tienda';
$string['single_purchase'] = 'Compra individual';
$string['single_purchase_price'] = 'Precio de compra individual';
$string['single_purchase_price_help'] = 'Precio por una licencia individual';
$string['single_purchase_validlength'] = 'Longitud válida (días)';
$string['single_purchase_validlength_help'] = 'El usuario se inscribirá en el curso durante este número de días después de la primera inscripción. Después de esto, será eliminado automáticamente (completamente) del curso';
$string['single_purchase_shelflife'] = 'Vida útil (días)';
$string['single_purchase_shelflife_help'] = 'El usuario debe inscribirse en el curso dentro de este número de días o la licencia expirará.';
$string['state'] = 'Provincia';
$string['status'] = 'Estado';
$string['status_b'] = 'Cesta';
$string['status_u'] = 'Sin pagar';
$string['status_p'] = 'Pagado';
$string['tags'] = 'Categorías';
$string['tags_help'] = 'Categorice este producto añadiendo categorías separadas por comas. Puede seleccionar categorías existentes o añadir nuevas que se crearán.';
$string['tagnameupdated'] = 'Nombre de etiqueta de tienda actualizado';
$string['total'] = 'Total';
$string['type_quantity_1_singlepurchase'] = 'Compra individual';
$string['type_quantity_n_singlepurchase'] = 'Compra individual';
$string['type_quantity_1_licenseblock'] = '{$a} licencia';
$string['type_quantity_n_licenseblock'] = '{$a} licencias';
$string['unitprice'] = 'Precio por licencia';
$string['uniquifyshoptagstask'] = 'Crear registros únicos para etiquetas de tienda para cada empresa';
$string['unprocessed'] = 'Sin procesar';
$string['unprocesseditems'] = 'Artículos sin procesar';
$string['useexternalshop'] = 'Usar una solución de comercio electrónico externa para compras';
$string['useexternalshop_help'] = 'Habilite esto si tiene una solución de comercio electrónico externa que tenga los servicios web correctos para trabajar con IOMAD.';
$string['value'] = 'Valor';
$string['xshopitems'] = 'Artículos de tienda de {$a}';
$string['xshoptag'] = 'Etiqueta de tienda {$a}';
