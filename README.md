# 📘 Control Tienda de Ropa

---

## Facultad de Informática Culiacán  
**Materia:** Ingeniería de Software  
**Maestro:** Fidel Bojórquez Solis  
**Grupo y Turno:** 3-5 Vespertino  

---

## Proyecto
**Control Tienda de Ropa**

---

## Integrantes

- Castro Pacheco Rigoberto  
- Coronel Carol Oliver  
- Guzmán Millán Jesús Eduardo  
- Quintero Parra Zadkiel Uriel  
- Rivera del Campo José Armand  
- Sillas Guerrero Lenin  
- Soto Ramírez Edith Marcela  
- Valdez Ezequiel  

---

## 1. Introducción

El presente proyecto consiste en el desarrollo de un **Sistema Web para el Control de una Tienda de Ropa**, cuyo objetivo es automatizar los procesos administrativos y comerciales como la gestión de productos, inventario, ventas y clientes.

El sistema busca reemplazar procesos manuales por una solución digital eficiente, segura y fácil de usar, mejorando tanto la administración interna como la experiencia del cliente.

---

## 2. Definición del Proyecto

El proyecto consiste en el desarrollo de un **Sistema Web para una Tienda de Ropa**, una aplicación informática que permite gestionar de manera eficiente los procesos comerciales y administrativos de una tienda de moda.

El sistema permitirá administrar inventario, ventas, clientes, catálogo de productos, control de existencias y generación de reportes.

El sistema será utilizado por el personal administrativo y por clientes registrados, con el fin de automatizar tareas manuales, reducir errores y mejorar la experiencia de compra tanto en línea como dentro de la tienda.

---

## 3. Justificación

Actualmente, muchas tiendas de ropa aún gestionan sus operaciones mediante procesos manuales o herramientas poco eficientes como hojas de cálculo, lo que provoca diversos problemas, tales como:

- Dificultad para controlar existencias.
- Errores al registrar ventas y productos.
- Poca visibilidad del comportamiento de clientes y de los productos más vendidos.
- Limitaciones en la experiencia del cliente al realizar compras o consultar productos.

La implementación de un sistema web automatizado permitirá:

- Mejorar la precisión y rapidez en los procesos administrativos.
- Mantener información actualizada en tiempo real sobre inventario y ventas.
- Facilitar las compras en línea a los clientes desde cualquier dispositivo.
- Generar reportes que permitan una mejor toma de decisiones.

---

## 4. Descripción del Sistema

El sistema permitirá a los administradores registrar productos, categorías, realizar control de inventario, gestionar ventas, administrar clientes y generar reportes.

Los clientes podrán consultar el catálogo de productos, agregar artículos al carrito, realizar compras y revisar su historial de pedidos.

El sistema será completamente web y contará con autenticación de usuarios y roles diferenciados (**Administrador / Cliente**).

---

## 5. Objetivos

### Objetivo General

Desarrollar un sistema web para una tienda de ropa que automatice y optimice los procesos de gestión de productos, ventas, clientes e inventario, garantizando eficiencia, seguridad y accesibilidad.

### Objetivos Específicos

1. Registrar y gestionar productos (nombre, talla, color, precio, categoría, stock, imágenes, etc.).
2. Administrar clientes con distintos permisos y perfiles.
3. Automatizar el proceso de compra y venta, incluyendo carrito de compras y procesos de pago.
4. Controlar existencias en tiempo real y alertar cuando los productos alcancen niveles mínimos.
5. Permitir búsquedas avanzadas y consultas del catálogo de productos de manera rápida y eficiente.
6. Generar reportes de ventas, productos más vendidos, clientes frecuentes y estadísticas de rendimiento.

---

## 6. Resumen del Sistema

El sistema permite a los **administradores** gestionar productos, categorías, usuarios, inventario y ventas, así como generar reportes y facturas electrónicas.

Los **usuarios** pueden consultar el catálogo de productos, buscar artículos, agregar productos al carrito, realizar compras y recibir notificaciones por correo electrónico.

Es una aplicación **100% web**, con autenticación de usuarios y control de acceso por roles.

---
## 7. Funcionalidades Principales

| **Funcionalidad**              | **Descripción general**                                                                 |
|--------------------------------|------------------------------------------------------------------------------------------|
| Gestión de inventario          | Registrar productos, eliminar productos y reabastecer productos.                         |
| Catálogo de productos          | Registrar categorías, modificar categorías y eliminar categorías.                        |
| Gestión de usuarios            | Creación de cuentas, inicio de sesión y asignación de roles (encargado, usuario).        |
| Carrito de compras             | Añadir productos, eliminar productos y modificar la cantidad seleccionada.               |
| Proceso de compra              | Rellenar formulario, agregar método de pago y confirmar pedido.                           |
| Barra de navegación (buscador) | Búsqueda de productos dentro del catálogo.                                                |
| Generación de facturas         | Generación de tickets electrónicos y reportes de ventas.                                  |
| Notificaciones                 | Envío de alertas por correo electrónico.                                                  |

---

## 8. Requisitos

### Requisitos Funcionales (RF)

| **Clave** | **Descripción Detallada** |
|----------|----------------------------|
| RF-01 | El sistema debe permitir al encargado registrar nuevos productos con sus datos (nombre, categoría, género, talla, material, color, tipo de prenda, marca, precio). |
| RF-02 | El sistema debe permitir registrar categorías (camisas, pantalones, tenis). |
| RF-03 | El sistema debe permitir registrar usuarios con nombre, correo y contraseña. |
| RF-04 | El sistema debe permitir añadir productos al carrito de compras y acumular todos los productos añadidos, tomando en cuenta la cantidad seleccionada y realizando el cálculo total del precio a pagar. |
| RF-05 | El sistema debe permitir rellenar la información solicitada durante el proceso de compra, agregar un método de pago y validar el pago para confirmar el pedido. |
| RF-06 | El sistema debe permitir realizar búsquedas de productos mediante parámetros como nombre, color, talla, tipo, marca, género y categoría. |
| RF-07 | El sistema debe generar tickets electrónicos que serán enviados al usuario al realizar una compra. |
| RF-08 | El sistema debe generar reportes de ventas, incluyendo ventas totales, productos más vendidos, clientes frecuentes y estadísticas de rendimiento, basándose en los tickets electrónicos. |
| RF-09 | El sistema debe enviar notificaciones por correo electrónico para autenticar cuentas, recibir promociones y avisar sobre poco stock de productos añadidos al carrito. |

### Requisitos No Funcionales (RNF)

| **Clave** | **Descripción** |
|----------|-----------------|
| RNF-01 | El sistema debe encriptar las contraseñas. |
| RNF-02 | El sistema debe proteger la información confidencial de los usuarios. |
| RNF-03 | El sistema debe tener buen rendimiento (respuesta rápida). |
| RNF-04 | El sistema debe ser fácil de usar y entender. |
| RNF-05 | El sistema debe ser seguro (autenticación con contraseña). |
| RNF-06 | El sistema debe estar disponible el 99% del tiempo. |
| RNF-07 | El sistema debe recibir mantenimiento constantemente. |
| RNF-08 | El sistema debe ser escalable para soportar hasta 100,000 usuarios. |
| RNF-09 | El sistema debe respaldarse diariamente. |
| RNF-10 | El sistema debe ser atractivo gráficamente. |
| RNF-11 | El sistema debe contar con una opción para solicitar devoluciones. |

### c) Requisitos Técnicos

- **Tipo de aplicación:** Sistema Web
- **Arquitectura:** Cliente – Servidor
- **Lenguaje del servidor:** PHP
- **Lenguajes del cliente:** HTML5, CSS3, JavaScript
- **Base de datos:** MySQL
- **Servidor web:** Apache (XAMPP)
- **Gestor de base de datos:** phpMyAdmin

**Seguridad**
- Encriptación de contraseñas mediante hash.
- Uso de sesiones para autenticación.
- Control de acceso por roles (Administrador / Usuario).

**Compatibilidad**
- Compatible con navegadores modernos (Chrome, Edge, Firefox).
- Accesible desde computadoras.

---
## 9. Actores Principales

| **Actor** | **Descripción de función** |
|----------|-----------------------------|
| **Administrador** | Persona encargada de gestionar productos, catálogos, usuarios y tiene acceso total al sistema. |
| **Usuario** | Persona que puede ver el catálogo, utilizar el carrito de compras, interactuar con el proceso de compra, usar la barra de navegación para consultar productos y recibir notificaciones. |
| **Sistema de notificaciones** | Elemento encargado de enviar notificaciones automáticas a los usuarios por correo electrónico. |

---

## 10. Casos de Uso

### a) Diagramas

El sistema cuenta con diagramas de casos de uso donde se representan las interacciones entre los actores principales (Administrador y Usuario) y las funcionalidades del sistema.

![Caso de Uso 1](imagenes\image-6.png)

---
![caso de Uso 2](imagenes\image-7.png)

---
![Caso de Uso 3](imagenes\image-8.png)

### b) Descripción

Los casos de uso describen las acciones que los usuarios pueden realizar dentro del sistema, como la gestión de productos, compras, generación de facturas y consultas del catálogo.


![R. Producto](imagenes\image-9.png)

![Modificar](imagenes\image-10.png)

![R. Categoria](imagenes\image-11.png)

![R. Usuario](imagenes\image-12.png)

![Agre. Carrito](imagenes\image-13.png)

![Procesar Compra](imagenes\image-14.png)

![Buscar Prod.](imagenes\image-15.png)

![Iniciar Sesion](imagenes\image-16.png)

![Modificar Usuario](imagenes\image-17.png)

![Eliminar Usuario](imagenes\image-18.png)

![Recup. Contraseña](imagenes\image-19.png)

---

## 11. Entidades, Atributos y Relaciones incluyendo cardinalidad, el Diagrama de Entidad-Relacion y Diagrama de Clases.

### Entidades Principales

#### ActividadSesion
- **ID_Registro** (PK)
- **ID_Usuario** (FK)
- **FechaInicio** (datetime)
- **FechaFin** (datetime)

#### Roles
- **ID_Rol** (PK)
- **NombreRol** (varchar)

#### Usuarios
- **ID_Usuario** (PK)
- **Nombre** (varchar)
- **Correo** (varchar)
- **Contraseña** (varchar)
- **ID_Rol** (FK)
- **Estado** (tinyint)
- **FechaCreacion** (datetime)

#### Productos
- **id_producto** (PK)
- **nombre** (varchar)
- **precio** (decimal)
- **categoria** (FK)
- **imagen** (varchar)

#### Categorias
- **id_categoria** (PK)
- **nombre** (varchar)
- **ParentID** (int, opcional)

#### Tallas
- **id_talla** (PK)
- **nombre** (varchar)
- **orden** (int)

#### Producto_Tallas
- **id_producto_talla** (PK)
- **id_producto** (FK)
- **id_talla** (FK)
- **stock** (int)

#### Carrito
- **id_carrito** (PK)
- **id_usuario** (FK)
- **id_producto** (FK)
- **id_talla** (FK)
- **cantidad** (int)
- **total** (decimal)
- **fecha_agregado** (datetime)

#### Verificaciones
- **ID** (PK)
- **Nombre** (varchar)
- **Correo** (varchar)
- **ContraseñaHash** (varchar)
- **Token** (varchar)
- **Codigo** (varchar)
- **ExpiresAt** (datetime)
- **CreatedAt** (datetime)

#### Recuperaciones
- **ID** (PK)
- **Correo** (varchar)
- **Codigo** (varchar)
- **ExpiresAt** (datetime)
- **CreateAt** (datetime)

#### Pedidos
- **id_pedido** (PK)
- **id_usuario** (FK)
- **fecha_pedido** (datetime)
- **total** (decimal)

#### Detalle_Pedido
- **id_pedido** (FK)
- **id_producto** (FK)
- **cantidad** (int)
- **precio_unitario** (decimal)

#### Facturas
- **id_factura** (PK)
- **id_usuario** (FK)
- **id_pedido** (FK)
- **ruta_pdf** (varchar)
- **fecha_creacion** (datetime)

---

### Relaciones

- **Usuario → ActividadSesion (1:N)**  
  Un usuario puede tener muchos registros de sesiones.

- **Rol → Usuario (1:N)**  
  Un rol puede asignarse a muchos usuarios.

- **Categoria → Producto (1:N)**  
  Una categoría contiene muchos productos.  
  Cada producto pertenece a una categoría.

- **Producto → Producto_Tallas (1:N)**  
  Un producto puede tener varias tallas con stock diferente.

- **Talla → Producto_Tallas (1:N)**  
  Una talla puede existir en múltiples productos.

- **Usuario → Carrito (1:N)**  
  Un usuario puede tener varios registros en el carrito.

- **Producto → Carrito (1:N)**  
  Un producto puede aparecer en muchos carritos.

- **Talla → Carrito (1:N)**  
  Una talla puede estar asociada a varios productos en el carrito.

- **Usuario → Verificaciones (1:N)**  
  Un usuario puede tener varios intentos de verificación.

- **Usuario → Recuperaciones (1:N)**  
  Un usuario puede generar múltiples solicitudes de recuperación.

- **Usuario → Pedido (1:N)**  
  Un usuario puede generar muchos pedidos.

- **Pedido → Detalle_Pedido (1:N)**  
  Un pedido contiene múltiples productos.  
  Cada detalle pertenece a un solo pedido.

- **Producto → Detalle_Pedido (1:N)**  
  Un producto puede aparecer en muchos detalles de pedido.

- **Pedido → Factura (1:1)**  
  Cada pedido genera una sola factura.

- **Usuario → Factura (1:N)**  
  Un usuario puede tener muchas facturas.


### Diagrama Entidad-Relación

El proyecto incluye un diagrama entidad-relación (DER) que representa gráficamente las entidades y sus relaciones.

![Diagrama Entidad-Relacion 1](imagenes\image.png)
![Diagrama Entidad-Relacion 2](imagenes\image-1.png)


### Diagrama de Clases

El diagrama de clases representa la estructura estática del sistema de la Tienda de Ropa, mostrando las clases principales, sus atributos, métodos y las relaciones entre ellas. Este modelo permite comprender cómo se organiza la información y cómo interactúan los distintos componentes del sistema.

![DiagramaDeClases1](imagenes\image20.png)
![DiagramaDeClases2](imagenes\image21.png)

---

## 12. Arquitectura del Sistema

El sistema utiliza una **arquitectura cliente-servidor**, donde el cliente accede mediante un navegador web y el servidor se encarga de la lógica del negocio y el acceso a la base de datos.

![alt text](imagenes\ArquitecturaDelSistema.jpeg)

---

## 13. Diseño de Interfaz (Figma)

El diseño de la interfaz fue realizado considerando la experiencia del usuario, con pantallas intuitivas como inicio de sesión, catálogo de productos, carrito de compras y panel de administración.

Link del Figma: https://www.figma.com/design/ays9LpusygmHimUlrwGAQ4/Online-shop?node-id=0-1&t=pOixQRhyBRR33uzh-1 

---

## 14. Estructura del Proyecto

El proyecto **proyecto_fibosol** está organizado de forma modular, separando la lógica del sistema, la interfaz de usuario, los recursos visuales y la base de datos, lo que facilita el mantenimiento y la comprensión del código.

- La carpeta **backend/** contiene la lógica del servidor, donde se gestionan las operaciones del sistema como el manejo de datos, procesos de negocio y comunicación con la base de datos.
- La carpeta **frontend/** almacena la interfaz de usuario, encargada de mostrar las vistas y permitir la interacción del usuario con el sistema.
- La carpeta **db/** y los archivos SQL incluyen los scripts necesarios para la creación y configuración de la base de datos.
- La carpeta **tables/** contiene definiciones y estructuras relacionadas con las tablas del sistema.
- La carpeta **img/** almacena imágenes utilizadas en el sistema y en la documentación.
- Las carpetas **styles/** y **js/** contienen los archivos CSS y JavaScript que controlan el diseño y el comportamiento del sistema en el navegador.
- La carpeta **fpdf186/** se utiliza para la generación de documentos PDF, como facturas o reportes.
- La carpeta **vendor/** junto con los archivos **composer.json** y **composer.lock** corresponden a las dependencias del proyecto gestionadas con Composer.
- El archivo **README.md** contiene la documentación general del proyecto.


![Estructura](imagenes\Estructura.jpeg)

---

## 15. Instalación y Configuración

Paso 1: Buscar en el navegador Composer install y entramos en el primer link:
![Paso 1](imagenes\image1.png)

Paso 2: Una vez dentro le damos Click donde dice Composer-Setup.exe y lo descargamos:
![Paso 2](imagenes\image2.png)

Paso 3: Nos vamos a Github para descargar el proyecton, nos metemos al respositorio Rigo31416Bruno / proyecto_fibosol, nos iriamos a la parte donde dice Code y copiamos el link.

![Paso 3](imagenes\image3.png)

Paso 4: Abrimos el CMD y nos vamos cambiando de directorio hasta llegar a la carpeta htdocs, usamos el comando git clone --branch master https://github.com/Rigo31416Bruno/proyecto_fibosol.git para descargar el proyecto en nuestra carpeta htdocs.
![Paso 4](imagenes\image4.png)

Paso 5: Nos cambiamos a la carpeta proyecto_fibosol y utilizamos el comando composer require phpmailer/phpmailer.
![Paso 5](imagenes\image5.png)

Paso 6: Nos vamos al navegador y buscamos fpdf y entramos al primer link, una vez dentro nos vamos a descargas y descargamos el ZIP del primer enlace.
![Paso 6](imagenes\image6.png)

Paso 7: una vez descargado el ZIP vamos a extraer el ZIP en si mismo , y una vez extraido vamos a copiar la carpeta con el nombre fpdf186 y la vamos a ir a pegar en nuestro proyecto_fibosol que se encontraria en la carpeta htdocs, deberia quedar asi:
![Paso 7](imagenes\image7.png)

Paso 8: Prenderiamos el xampp y nos iriamos al phpMyAdmin para crear una base de datos llamada inicio_sesion y importar el archivo de base de datos que se encuentra en la carpeta proyecto_fibosol:
![Paso 8](imagenes\image8.png)

Paso 9: Una vez cargada la base de datos, nos iriamos al navegador y bucariamos nuestro repositorio local poniendo localhots/proyecto_fibosol y ya estaria listo para poder usar.

---

## 16. Uso y Operación del Sistema

### Inicio de Sesión

El sistema cuenta con un **módulo de inicio de sesión** que permite a los usuarios acceder a la plataforma utilizando sus credenciales previamente registradas.

En caso de que el usuario **no cuente con una cuenta**, en la parte inferior de la pantalla se encuentra la opción **Registrarse**, mediante la cual podrá crear una nueva cuenta ingresando sus datos personales.

Asimismo, si el usuario **olvida su contraseña**, el sistema ofrece la opción **“¿Olvidaste tu contraseña?”**, la cual redirige a un formulario de recuperación. En dicho formulario, el usuario deberá ingresar su correo electrónico para recibir las instrucciones necesarias y restablecer su contraseña de manera segura.

Durante el proceso de inicio de sesión, el sistema valida los datos ingresados. Si el correo o la contraseña son incorrectos, se mostrará una **alerta indicando que los datos son erróneos**. En caso de que la información sea correcta, el sistema mostrará un **mensaje de inicio de sesión exitoso** y permitirá el acceso a la plataforma.

A continuación, se muestra la interfaz correspondiente al **inicio de sesión del sistema**:

![alt text](imagenes\image22.png)

### Registro

Al seleccionar la opción **Registrarse**, el sistema muestra un formulario en el que el usuario debe ingresar un **nombre de usuario**, un **correo electrónico** y una **contraseña**.

El sistema valida que el **correo electrónico no se encuentre previamente registrado**; en caso de que ya exista una cuenta asociada a dicho correo, se mostrará una **alerta indicando que el correo ya está registrado**.

La contraseña debe cumplir con ciertas **especificaciones de seguridad**, las cuales incluyen:
- Contener al menos **un número**.
- Incluir **un carácter especial**.
- Tener una longitud mínima de **8 caracteres**.

Si la contraseña no cumple con alguno de estos requisitos, el sistema no permitirá continuar con el registro y mostrará el mensaje correspondiente. En caso de que todos los datos sean correctos, la cuenta se creará exitosamente.

A continuación, se muestra el formulario correspondiente al **registro de usuarios**:


![alt text](imagenes\image23.png)

Para finalizar el proceso de registro y verificar que el **correo electrónico ingresado sea válido**, el sistema mostrará un **panel de verificación** donde el usuario deberá ingresar un **código de verificación** enviado automáticamente a su correo electrónico.

Si el usuario **no ingresa el código de verificación**, la cuenta **no será creada**, ya que este paso es obligatorio para confirmar la identidad del usuario. En caso de que el código ingresado sea incorrecto o haya expirado, el sistema mostrará un mensaje de error solicitando verificar la información.

Una vez que el usuario **ingrese correctamente el código de verificación**, el sistema confirmará la validación del correo y la **cuenta será creada exitosamente**, permitiendo al usuario acceder a la plataforma.

A continuación, se muestra el panel correspondiente para **ingresar el código de verificación**:

![alt text](imagenes\image24.png)

### Recuperación de Contraseña

Al seleccionar la opción **“¿Olvidaste tu contraseña?”**, el sistema mostrará un **panel de recuperación**, en el cual se solicitará al usuario ingresar el **correo electrónico previamente registrado**.

Si el correo electrónico **no se encuentra registrado**, el sistema mostrará una **alerta notificando que el correo no existe**. En caso de que el correo sea válido, el sistema habilitará una sección adicional donde se pedirá ingresar un **código de verificación** que será enviado al correo electrónico del usuario.

Si el código de verificación **no llega al correo**, el usuario contará con la opción de **reenviar el código de verificación**. Una vez que el código ingresado sea correcto y válido, el sistema permitirá continuar con el proceso.

Posteriormente, se mostrará un nuevo panel donde el usuario deberá **ingresar una nueva contraseña y confirmarla**. La contraseña deberá cumplir con las políticas de seguridad establecidas por el sistema.

Al completar correctamente este proceso, la contraseña será actualizada exitosamente y el usuario podrá **iniciar sesión con sus nuevas credenciales**.

A continuación, se muestra la interfaz correspondiente al **panel de recuperación de contraseña**:

![alt text](imagenes\image25.png)

### Nueva Contraseña

A continuación, se muestra el **panel para el establecimiento de la nueva contraseña**, donde el usuario deberá ingresar y confirmar su nueva contraseña.

La contraseña debe cumplir con las **especificaciones de seguridad establecidas por el sistema**, las cuales incluyen una longitud mínima y el uso de caracteres seguros. En caso de que la contraseña **no cumpla con estos requisitos**, el sistema no permitirá continuar con el proceso y mostrará el mensaje correspondiente.

Una vez que la nueva contraseña cumpla con todas las especificaciones y sea confirmada correctamente, el sistema **actualizará la contraseña de forma exitosa**, permitiendo al usuario iniciar sesión con sus nuevas credenciales.

A continuación, se muestra el panel correspondiente para **crear la nueva contraseña**:

![alt text](imagenes\image26.png)

### Página de Inicio

A continuación, se muestra la **página principal del sistema** una vez que el usuario ha iniciado sesión correctamente.

En la parte superior de la interfaz (encabezado o *header*), ubicada en la esquina izquierda, se muestra el **nombre de la página: Shopware**. El encabezado incluye un menú de navegación que permite al usuario desplazarse fácilmente por las distintas secciones del sitio.

El menú cuenta con las siguientes opciones:
- **Inicio:** Permite regresar al inicio de la página principal.
- **Camisas:** Redirige a la sección donde se muestran los productos correspondientes a camisas.
- **Pantalones:** Redirige a la sección específica de pantalones dentro de la página principal.
- **Tenis:** Redirige a la sección donde se encuentran los tenis disponibles.
- **Contacto:** Redirige a la parte final de la página, donde se muestra la información de contacto.
- **Iniciar sesión:** Permite acceder al sistema (visible cuando el usuario no ha iniciado sesión).
- **Registrarse:** Permite crear una nueva cuenta.
- **Carrito de compras:** Muestra los productos agregados al carrito y permite acceder al proceso de compra.

Este diseño facilita la navegación del usuario, permitiéndole acceder rápidamente a las distintas secciones sin necesidad de buscar manualmente los productos.

### Opciones de Búsqueda y Filtrado de Productos

En la parte inferior de la sección del encabezado, el sistema ofrece **tres opciones de búsqueda y filtrado**, las cuales permiten al usuario localizar productos de manera rápida y eficiente.

Las opciones disponibles son las siguientes:
- **Buscador por nombre:** Permite buscar un producto ingresando su nombre o una palabra clave relacionada.
- **Filtro por género:** Permite filtrar los productos según su clasificación, mostrando artículos para **hombre** o **mujer**.
- **Filtro por categoría:** Permite seleccionar una categoría específica del producto para visualizar únicamente los artículos relacionados.

Estas herramientas de búsqueda mejoran la experiencia del usuario, facilitando la localización de productos de interés y optimizando la navegación dentro del catálogo.

A continuación, se muestra la interfaz correspondiente a la **página de inicio del sistema**:

![alt text](imagenes\image27.png)

### Vista del Botón de Camisas

Al seleccionar el **botón “Camisas”** desde el menú de navegación, el sistema redirige al usuario automáticamente a la **sección de camisas** dentro de la página principal.

En esta vista se muestran únicamente los productos correspondientes a la categoría **Camisas**, permitiendo al usuario visualizar de forma ordenada la información de cada producto, como **imagen, nombre, precio y disponibilidad**.

Esta funcionalidad facilita la navegación y mejora la experiencia del usuario, ya que permite localizar rápidamente los productos de interés sin necesidad de desplazarse manualmente por toda la página.

A continuación, se muestra la vista correspondiente a la **sección de camisas**:


![alt text](imagenes\image28.png)

### Agregar Producto al Carrito

Al seleccionar el **botón “Agregar al carrito”**, el sistema muestra un **panel de selección** donde podemos elegir los detalles del producto antes de añadirlo al carrito de compras.

En este panel es posible:
- Seleccionar la **talla o medida** del producto.
- Indicar la **cantidad** deseada.
- Confirmar la acción mediante el botón **Agregar al carrito**.

El sistema valida automáticamente la **disponibilidad de stock**. En caso de que una talla no cuente con existencias, esta se mostrará **deshabilitada** y no permitirá ser seleccionada. Asimismo, cuando el stock de una talla se agota por completo, dicha opción queda bloqueada y no es posible agregar más unidades al carrito.

Este comportamiento se aplica de la misma manera para **todos los productos del sistema**, garantizando un control preciso del inventario y evitando pedidos sin disponibilidad.

A continuación, se muestra el panel correspondiente para **agregar productos al carrito**:


![alt text](imagenes\image34.png)

### Vista del Botón de Pantalones

Al seleccionar el **botón “Pantalones”** desde el menú de navegación, el sistema redirige al usuario a la **sección de pantalones** dentro de la página principal.

En esta vista se muestran exclusivamente los productos correspondientes a la categoría **Pantalones**, presentando de forma clara información como **imagen, nombre, precio y disponibilidad** de cada artículo.

Esta funcionalidad permite al usuario encontrar rápidamente los pantalones disponibles sin necesidad de recorrer todo el catálogo, mejorando la navegación y la experiencia de uso del sistema.

A continuación, se muestra la vista correspondiente a la **sección de pantalones**:

![alt text](imagenes\image29.png)

### Vista del Botón de Contacto

Al seleccionar el **botón “Contacto”** desde el menú de navegación, el sistema redirige automáticamente al usuario a la **sección de contacto**, ubicada en la parte inferior de la página principal.

En esta sección se muestra información relevante para la comunicación con la tienda, como:
- **Correo electrónico de contacto**.
- **Número telefónico**.
- **Enlaces a redes sociales**, permitiendo a los usuarios interactuar con la tienda a través de diferentes plataformas.

Además, esta vista incluye un apartado de **suscripción al newsletter**, donde los usuarios pueden ingresar su correo electrónico para recibir **ofertas exclusivas y novedades** relacionadas con los productos de la tienda.

Esta funcionalidad facilita la comunicación entre los usuarios y la tienda, mejorando la atención al cliente y fortaleciendo la relación con los compradores.

A continuación, se muestra la vista correspondiente a la **sección de contacto del sistema**:

![alt text](imagenes\image30.png)

### Menú del Carrito de Compras

Al seleccionar el **ícono del carrito de compras**, el sistema despliega un **menú con tres opciones principales**, las cuales permiten al usuario gestionar su experiencia dentro de la plataforma.

Las opciones disponibles son las siguientes:
1. **Ir al carrito:** Permite visualizar los productos agregados, modificar cantidades y continuar con el proceso de compra.
2. **Gestionar direcciones:** Permite administrar y cambiar la dirección de envío a la cual se entregará el pedido.
3. **Cerrar sesión:** Permite al usuario finalizar su sesión de manera segura y salir del sistema.

Este menú facilita el acceso rápido a funciones clave del sistema, mejorando la usabilidad y la navegación del usuario.

A continuación, se muestra el menú correspondiente al **carrito de compras**:

![alt text](imagenes\image31.png)


### Ir al Carrito

Al seleccionar la opción **“Ir al carrito”**, podemos visualizar todos los **productos agregados**, mostrando la **cantidad de cada artículo**, el **precio individual** y el **total acumulado de la compra**.

Dentro de este apartado, contamos con dos opciones principales:
- **Continuar comprando**, que nos redirige nuevamente al **catálogo de productos** para seguir agregando artículos.
- **Proceder al pago**, que nos lleva al siguiente apartado donde se realiza el **proceso de compra y pago**.

Este panel nos permite revisar el contenido del carrito antes de finalizar la compra, asegurando que los productos y cantidades seleccionadas sean correctos.

A continuación, se muestra el **panel de ir al carrito**:

![alt text](imagenes\image_13.jpeg)

### Proceder al Pago

Al seleccionar la opción **“Proceder al pago”**, accedemos al formulario donde debemos ingresar la **información necesaria para completar la compra**. En este apartado se solicitan los siguientes datos:

- Número de la tarjeta.
- Nombre del titular de la tarjeta.
- Fecha de vencimiento.
- Código de seguridad (CVV).
- Dirección de envío.

Una vez completados correctamente los datos, podemos **confirmar la compra** y finalizar el proceso de pago.

En la parte derecha de la pantalla, contamos con un **panel de resumen del pedido**, donde se visualizan los productos seleccionados, las cantidades y el **total a pagar**, lo que nos permite verificar la información antes de realizar la compra.

A continuación, se muestra el **panel de proceder al pago**:

![alt text](imagenes\image_14.png)


### Confirmación de Pago

Al presionar el botón **“Pagar”**, el sistema nos muestra un **panel de confirmación de pago**, cuyo objetivo es verificar que deseamos finalizar la compra.

En este panel se nos presentan dos opciones:
- **Confirmar pago**, para completar la transacción y generar el pedido correspondiente.
- **Cancelar**, para regresar al proceso de compra sin realizar el pago.

Una vez confirmada la operación, el sistema registra el pedido de forma exitosa y continúa con el proceso correspondiente, como la generación de la factura y la notificación al usuario.

A continuación, se muestra el **panel de confirmación de pago**:

![alt text](imagenes\image_15.png)

### Pago Confirmado

Al seleccionar la opción **“Confirmar pago”**, el sistema nos muestra un **panel de pago confirmado**, indicando que la transacción se realizó de manera exitosa.

En este panel se nos presentan dos opciones principales:
- **Generar factura**, que permite crear y descargar la factura correspondiente a la compra realizada.
- **Regresar al inicio**, opción que nos redirige a la página principal del sistema para continuar navegando o realizar nuevas compras.

De esta manera, el sistema garantiza que el usuario tenga acceso inmediato a la factura y pueda continuar utilizando la plataforma sin inconvenientes.

A continuación, se muestra el **panel de confirmación de pago**:


![alt text](imagenes\image_16.png)

### Gestión de Direcciones

Al seleccionar la opción **“Gestionar direcciones”**, el sistema redirige al usuario a un **apartado específico para administrar la dirección de envío**.

En esta sección contamos con la integración de un **mapa interactivo (Google Maps)**, el cual permite **seleccionar la dirección de forma automática** señalando directamente la ubicación en el mapa. De esta manera, el usuario puede registrar su dirección de manera más precisa y rápida, reduciendo errores en el envío de los pedidos.

Una vez seleccionada la ubicación, la dirección queda asociada a la cuenta del usuario para ser utilizada en futuras compras.

A continuación, se muestra el panel correspondiente a la **gestión de direcciones del sistema**:

![alt text](imagenes\image32.png)

### Cierre de Sesión

Al seleccionar la opción **“Cerrar sesión”**, el sistema muestra un **panel de confirmación** para evitar cierres accidentales de la sesión.

En este panel se presentan **dos opciones**:
- **Cancelar:** Permite regresar al sistema sin cerrar la sesión.
- **Confirmar salida:** Finaliza la sesión de manera segura y redirige al usuario a la pantalla de inicio.

Esta funcionalidad garantiza una mejor experiencia de usuario al confirmar la acción antes de salir del sistema.

A continuación, se muestra el panel correspondiente al **cierre de sesión del sistema**:

![alt text](imagenes\image33.png)


### Acceso al Panel de Administrador

Para acceder al **panel de administrador**, utilizamos una cuenta con permisos administrativos. En este caso, se emplea el correo electrónico **rigotrainer@gmail.com** junto con su contraseña correspondiente que seria **Rigoberto18;**.

Al iniciar sesión con estas credenciales, el sistema nos redirige a la **vista del administrador**, donde es posible acceder a todas las funcionalidades exclusivas de este rol.

Desde el panel de administrador se pueden realizar acciones como:
- Gestión de productos y categorías.
- Administración de usuarios.
- Control de inventario.
- Visualización de pedidos y generación de reportes.
- Supervisión general del sistema.

Este panel permite al administrador tener un control completo sobre el funcionamiento de la tienda y la información almacenada en el sistema.

A continuación, se muestra la vista correspondiente al **inicio de la página del administrador**:

![alt text](imagenes\image35.png)

### Dashboard del Administrador

En la vista del **Dashboard**, contamos con un **resumen general del sistema**, el cual permite al administrador visualizar de manera rápida la información más relevante de la tienda.

En esta sección se muestran los siguientes indicadores:
- **Total de productos** registrados.
- **Total de usuarios** en el sistema.
- **Total de pedidos** realizados.
- **Total de ventas** generadas.

Además, se incluye una **tabla de pedidos recientes**, donde se visualiza información clave como:
- **ID del pedido**.
- **Cliente que realizó la compra**.
- **Fecha del pedido**.
- **Total de la compra**.
- **Botón de acción “Ver”**, el cual redirige al apartado de pedidos para consultar el detalle completo.

Esta vista facilita el monitoreo del estado general del sistema y apoya la toma de decisiones administrativas.

A continuación, se muestra el panel correspondiente al **Dashboard del administrador**:

![alt text](imagenes\image_1.png)

### Gestión de Productos (Administrador)s

En la sección de **Productos** del panel de administrador, podemos visualizar **todos los productos registrados** en el sistema de forma organizada.

Esta vista cuenta con una **barra de filtrado y búsqueda**, que permite:
- Buscar productos por **nombre**.
- Filtrar por **género**.
- Filtrar por **categoría**.
- Ordenar por **productos más recientes**.

Estas opciones facilitan la localización y administración de los productos.

En la **tabla de productos**, se muestra la información principal de cada artículo y se dispone de las siguientes acciones:
- **Editar:** Permite modificar los datos del producto.
- **Eliminar:** Permite borrar un producto del sistema.

Adicionalmente, en la parte superior de la vista se encuentra el botón **“Agregar producto”**, el cual despliega un **formulario** para registrar un nuevo producto en el sistema.

Esta sección permite al administrador mantener actualizado el catálogo y el control del inventario de manera eficiente.

A continuación, se muestra el panel correspondiente a la **gestión de productos del administrador**:

![alt text](imagenes\image_2.png)

### Agregar Nuevo Producto (Administrador)

Al seleccionar el botón **“Nuevo producto”**, el sistema despliega un **formulario** que permite al administrador registrar un producto nuevo dentro del catálogo.

En este formulario es posible ingresar la siguiente información:
- **Nombre del producto**.
- **Precio**.
- **Categoría**.
- **Imagen del producto**.
- **Stock por talla**, permitiendo definir la disponibilidad de cada medida.

El sistema valida que la información sea correcta antes de guardar el producto, asegurando un control adecuado del inventario y una correcta visualización en la tienda.

Una vez completado el registro, el producto queda disponible para su visualización y compra por parte de los clientes.

A continuación, se muestra el formulario correspondiente para **agregar un nuevo producto**:

![alt text](imagenes\image_3.png)

### Editar y Eliminar Productos (Administrador)

Dentro de la **gestión de productos**, contamos con opciones que permiten **modificar o eliminar** los productos registrados en el sistema.

- **Editar producto:**  
  Al seleccionar esta opción, el sistema muestra un **panel de edición** donde podemos modificar cualquier información del producto, como nombre, precio, categoría, imagen o stock por talla. Los cambios se guardan una vez confirmados.

- **Eliminar producto:**  
  Al seleccionar esta opción, el sistema despliega un **mensaje de confirmación**, ofreciendo las opciones de **eliminar** o **cancelar** la acción. Esto evita la eliminación accidental de productos.

Estas funcionalidades permiten al administrador mantener el catálogo actualizado y gestionar los productos de manera segura y eficiente.

A continuación, se muestra el panel correspondiente a las opciones de **editar y eliminar productos**:

![alt text](imagenes\image_4.png)
![alt text](imagenes\image_5.png)


### Gestión de Usuarios (Administrador)

En el **panel de usuarios** del administrador, podemos visualizar un **resumen general de los usuarios registrados** en el sistema.

En esta sección se muestran indicadores como:
- **Total de usuarios**.
- **Total de clientes**.
- **Total de administradores**.

Además, contamos con diversas herramientas de administración que facilitan la gestión de usuarios:
- Opción para **crear un nuevo usuario**.
- **Barra de búsqueda**, que permite localizar usuarios por **nombre o correo electrónico**.
- Filtros por **rol** y por **usuarios más recientes**, lo que facilita la organización de la información.

En la **tabla de usuarios**, se muestra información detallada de cada registro, incluyendo:
- **ID del usuario**.
- **Nombre**.
- **Correo electrónico**.
- **Rol**.
- **Teléfono**.
- **Estado**.
- **Acciones**, que permiten **editar** o **eliminar** usuarios.

Esta sección permite al administrador llevar un control completo de los usuarios del sistema y gestionar sus permisos de manera eficiente.

A continuación, se muestra el panel correspondiente a la **gestión de usuarios del administrador**:

![alt text](imagenes\image_6.png)

### Crear Nuevo Usuario (Administrador)

Al seleccionar el botón **“Nuevo usuario”**, el sistema despliega un **formulario** que permite al administrador registrar un nuevo usuario dentro de la plataforma.

En este formulario podemos ingresar la siguiente información:
- **Nombre del usuario**.
- **Correo electrónico**.
- **Contraseña**.
- **Rol** asignado.
- **Teléfono**.
- **Dirección**.

Una vez completados los campos y confirmada la acción, el sistema **guarda el nuevo usuario** y este queda registrado para acceder a la plataforma según el rol asignado.

Esta funcionalidad permite al administrador gestionar de manera eficiente a los usuarios del sistema.

A continuación, se muestra el formulario correspondiente para **crear un nuevo usuario**: 

![alt text](imagenes\image_7.png)

### Gestión de Pedidos (Administrador)

En el **panel de pedidos** del administrador, podemos visualizar **todos los pedidos registrados** en el sistema de manera ordenada.

En esta sección se muestra información relevante de cada pedido, como:
- **Usuario que realizó el pedido**.
- **Correo electrónico del usuario**.
- **Fecha del pedido**.
- **Total de la compra**.

Como acción principal, contamos con la opción **“Ver”**, la cual abre un **modal con el detalle del pedido**, permitiendo consultar los productos adquiridos, cantidades y precios.

Adicionalmente, en la parte superior del panel se incluye un **buscador**, que permite localizar pedidos por **nombre del usuario o correo electrónico**, así como un **filtro por rango de fechas**. Estas herramientas facilitan la búsqueda y hacen más eficiente la gestión de los pedidos.

A continuación, se muestra el panel correspondiente a la **gestión de pedidos del administrador**:

![alt text](imagenes\image_8.png)

### Detalle del Pedido (Administrador)

Si damos clic en el botón **“Ver detalles”**, se nos mostrará un **panel/modal con la información completa del pedido seleccionado**.

En esta vista podemos consultar:
- **Datos del cliente**, como nombre y correo electrónico.
- **Fecha en la que se realizó el pedido**.
- **Lista de productos adquiridos**, mostrando el nombre del producto, la talla seleccionada, la cantidad y el precio unitario.
- **Total final de la compra**.

Esta sección nos permite **verificar y validar la información del pedido**, facilitando el control administrativo y el seguimiento de las ventas realizadas.

A continuación, se muestra el panel correspondiente al **detalle del pedido**:

![alt text](imagenes\image_9.png)


### Gestión de Categorías (Administrador)

En el apartado de **Categorías** del administrador, podemos **filtrar y buscar categorías** utilizando la barra de navegación o seleccionando una categoría específica, lo que facilita la localización de información dentro del sistema.

También contamos con la opción de **agregar una nueva categoría**, donde es posible ingresar el **nombre de la categoría** y, de manera opcional, definir una **subcategoría**, permitiendo una mejor organización del catálogo de productos.

Finalmente, se muestra un **listado general de las categorías registradas**, junto con sus respectivas subcategorías, lo que nos permite visualizar y gestionar de forma clara la estructura de categorías del sistema.

A continuación, se presenta el panel correspondiente a la **gestión de categorías**:


![alt text](imagenes\image_10.png)

### Gestión de Facturas (Administrador)

En el apartado de **Facturas** del administrador, podemos **visualizar todas las facturas registradas** en el sistema. Para facilitar su búsqueda, contamos con opciones de **filtrado**, ya sea utilizando la barra de navegación para buscar una factura por **nombre del cliente o correo electrónico**, o bien seleccionando un **rango de fechas**.

Dentro de la tabla de listado de facturas, disponemos de dos acciones principales: **visualizar la factura** directamente en el sistema o **descargarla** en formato PDF, lo que permite un mejor control y respaldo de la información.

A continuación, se muestra el panel correspondiente a la **gestión de facturas**:

![alt text](imagenes\image_11.png)

### Visualización de Factura (Abrir PDF)

Si seleccionamos la opción **“Abrir PDF”**, el sistema nos muestra la **factura generada en formato PDF**, donde podemos visualizar de manera clara y ordenada los datos principales de la compra.

En esta factura se incluye información como los **datos del cliente**, el **detalle de los productos adquiridos**, las **cantidades**, los **precios unitarios**, el **total de la compra** y la **fecha de emisión**.  

Esta funcionalidad nos permite **consultar, revisar y validar** la información de cada venta, así como contar con un respaldo digital que puede ser descargado o impreso en caso de ser necesario.

A continuación, se muestra la **visualización de una factura en PDF**:

![alt text](imagenes\image_12.png)



---

## 17. Base de Datos (Modelado)

La base de datos está diseñada con claves primarias y foráneas para garantizar la integridad de la información y una correcta relación entre las tablas.

-- =========================================
-- CREAR BASE DE DATOS
-- =========================================
CREATE DATABASE IF NOT EXISTS iniciar_sesion
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE iniciar_sesion;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =========================================
-- TABLA: roles
-- =========================================
CREATE TABLE roles (
  ID_Rol INT NOT NULL AUTO_INCREMENT,
  NombreRol VARCHAR(100),
  PRIMARY KEY (ID_Rol)
) ENGINE=InnoDB;

INSERT INTO roles VALUES
(1, 'Cliente'),
(2, 'Administrador');

-- =========================================
-- TABLA: usuarios
-- =========================================
CREATE TABLE usuarios (
  ID_Usuario INT NOT NULL AUTO_INCREMENT,
  Nombre VARCHAR(100),
  Correo VARCHAR(100) UNIQUE,
  Contraseña VARCHAR(255),
  ID_Rol INT,
  Direccion VARCHAR(100),
  Telefono CHAR(10),
  Estatus VARCHAR(50),
  PRIMARY KEY (ID_Usuario),
  FOREIGN KEY (ID_Rol) REFERENCES roles(ID_Rol)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: actividadsesion
-- =========================================
CREATE TABLE actividadsesion (
  ID_Registro INT NOT NULL AUTO_INCREMENT,
  ID_Usuario INT,
  FechaInicio DATETIME,
  FechaFin DATETIME,
  PRIMARY KEY (ID_Registro)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: categorias
-- =========================================
CREATE TABLE categorias (
  id_categoria INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  ParentID INT DEFAULT NULL,
  PRIMARY KEY (id_categoria),
  FOREIGN KEY (ParentID) REFERENCES categorias(id_categoria)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: productos
-- =========================================
CREATE TABLE productos (
  id_producto INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100),
  precio DECIMAL(10,2),
  categoria INT,
  imagen VARCHAR(255),
  PRIMARY KEY (id_producto),
  FOREIGN KEY (categoria) REFERENCES categorias(id_categoria)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: tallas
-- =========================================
CREATE TABLE tallas (
  id_talla INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(10),
  orden INT,
  PRIMARY KEY (id_talla)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: producto_tallas
-- =========================================
CREATE TABLE producto_tallas (
  id_producto_talla INT NOT NULL AUTO_INCREMENT,
  id_producto INT,
  id_talla INT,
  stock INT DEFAULT 0,
  PRIMARY KEY (id_producto_talla),
  UNIQUE (id_producto, id_talla),
  FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
  FOREIGN KEY (id_talla) REFERENCES tallas(id_talla)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: carrito
-- =========================================
CREATE TABLE carrito (
  id_carrito INT NOT NULL AUTO_INCREMENT,
  id_usuario INT,
  id_producto INT,
  id_producto_talla INT,
  cantidad INT DEFAULT 1,
  total DECIMAL(10,2),
  fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_carrito),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(ID_Usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================
-- TABLA: pedidos
-- =========================================
CREATE TABLE pedidos (
  id_pedido INT NOT NULL AUTO_INCREMENT,
  id_usuario INT,
  fecha_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(10,2),
  PRIMARY KEY (id_pedido),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(ID_Usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================
-- TABLA: detalle_pedido
-- =========================================
CREATE TABLE detalle_pedido (
  id_pedido INT,
  id_producto INT,
  id_producto_talla INT,
  cantidad INT,
  precio_unitario DECIMAL(10,2),
  FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
  FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE,
  FOREIGN KEY (id_producto_talla) REFERENCES producto_tallas(id_producto_talla) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================
-- TABLA: facturas
-- =========================================
CREATE TABLE facturas (
  id_factura INT NOT NULL AUTO_INCREMENT,
  id_usuario INT,
  id_pedido INT,
  ruta_pdf VARCHAR(255),
  fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_factura),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(ID_Usuario) ON DELETE CASCADE,
  FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================
-- TABLA: recuperaciones
-- =========================================
CREATE TABLE recuperaciones (
  ID INT NOT NULL AUTO_INCREMENT,
  Correo VARCHAR(255),
  Codigo VARCHAR(32),
  ExpiresAt DATETIME,
  CreatedAt DATETIME,
  PRIMARY KEY (ID)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: verificaciones
-- =========================================
CREATE TABLE verificaciones (
  ID INT NOT NULL AUTO_INCREMENT,
  Nombre VARCHAR(100),
  Correo VARCHAR(100),
  ContrasenaHash VARCHAR(255),
  Token VARCHAR(64) UNIQUE,
  Codigo VARCHAR(16),
  ExpiresAt DATETIME,
  CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID)
) ENGINE=InnoDB;

COMMIT;



---

## 18. Conclusión acerca del trabajo

El desarrollo del sistema **Control Tienda de Ropa** nos permitió aplicar de manera práctica los conocimientos adquiridos en el análisis, diseño y desarrollo de sistemas web, logrando una solución funcional que automatiza los procesos administrativos y comerciales de una tienda de ropa.

A lo largo del proyecto definimos claramente los objetivos, requisitos, actores y funcionalidades del sistema, los cuales fueron implementados mediante una arquitectura cliente-servidor, garantizando una correcta organización, seguridad y escalabilidad. El sistema facilita la gestión de productos, inventario, ventas, clientes y facturación, reduciendo errores derivados de procesos manuales y mejorando la eficiencia operativa.

Asimismo, el modelado de la base de datos, junto con los diagramas de casos de uso, entidad-relación y clases, nos permitió estructurar correctamente la información y asegurar la integridad de los datos. La inclusión de funcionalidades como el carrito de compras, la generación de facturas en PDF y las notificaciones por correo electrónico mejora significativamente la experiencia del usuario.

En conclusión, el sistema cumple con los objetivos planteados y representa una herramienta confiable, eficiente y adaptable, que puede ser utilizada como base para futuras mejoras o ampliaciones, demostrando la importancia del trabajo en equipo y del uso de sistemas web en la modernización de procesos comerciales.

