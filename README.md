# WordPress Accessibility Plugins

Plugins de accesibilidad para WordPress diseñados y desarrollados por [Diegor.tech](https://diegor.tech/).

| Plugin | Versión | Descripción |
|---|---|---|
| [Diegor Accesibilidad](./diegor-accesibilidad) | 1.0.2 | Barra de accesibilidad para personas con discapacidad visual y auditiva. |

---

## Diegor Accesibilidad

*Plugin de accesibilidad para WordPress · Diseñado y desarrollado por Diegor.tech*

Diegor Accesibilidad añade un botón flotante con una barra de herramientas que permite a cada visitante adaptar el sitio a sus necesidades. Está pensado para Colombia: incluye un intérprete de Lengua de Señas Colombiana (LSC) por página y un lector de voz en español. Las preferencias del visitante se guardan en su navegador para la próxima visita.

### Para personas con discapacidad visual

- Tamaño del texto ajustable del 80 % al 180 %, incluso en temas que usan tamaños en píxeles.
- Alto contraste, modo oscuro, colores invertidos y escala de grises.
- Lector de voz en español: lee la página completa, el texto seleccionado o el bloque en el que se hace clic, con control de velocidad.
- Fuente legible, mayor espaciado de texto (WCAG 1.4.12), enlaces resaltados y foco visible reforzado.
- Cursor grande, guía de lectura y lupa de texto.
- Opción para detener las animaciones, activada automáticamente si el sistema pide reducir el movimiento.

### Para personas con discapacidad auditiva

- Subtítulos en español activados automáticamente en videos HTML5 y de YouTube.
- Subtítulos grandes con fondo sólido.
- Intérprete de Lengua de Señas Colombiana en una ventana flotante, configurable en cada página.
- Transcripción de audios y videos, en una ventana o dentro del contenido con el shortcode `[da_transcripcion]`.
- Título accesible automático para los iframes que no lo tienen.

### Para el administrador

- Configuración en **Ajustes → Accesibilidad**.
- Cuatro posiciones del botón: abajo o al centro, a la derecha o a la izquierda.
- Color personalizable, con aviso si el contraste es menor a 4.5:1.
- Manejable con teclado (`Alt + A` para abrir, `Esc` para cerrar) y compatible con lectores de pantalla.
- Sin cookies, sin servicios externos y sin librerías de terceros.
- Compatible con Elementor y con cualquier tema de WordPress.

### Requisitos

- WordPress 6.3 o superior
- PHP 7.4 o superior

### Instalación

1. Descarga la carpeta [`diegor-accesibilidad`](./diegor-accesibilidad) y comprímela en un `.zip`, o descarga el `.zip` desde la sección **Releases**.
2. En WordPress ve a **Plugins → Añadir nuevo → Subir plugin**, selecciona el `.zip` y actívalo.
3. Configura la barra en **Ajustes → Accesibilidad**.
4. En cada página o entrada, usa la caja **«Accesibilidad auditiva»** para añadir el video del intérprete LSC y la transcripción.

Si ya tienes otro plugin de accesibilidad con botón flotante, desactívalo para no mostrar dos barras.

### Subtítulos propios en Elementor

El widget «Video» de Elementor no permite añadir archivos de subtítulos. Usa un widget «HTML» con este código:

```html
<figure class="video-accesible">
  <video controls preload="metadata" playsinline>
    <source src="https://tusitio.com/wp-content/uploads/video.mp4" type="video/mp4">
    <track kind="captions" srclang="es" label="Español" default
           src="https://tusitio.com/wp-content/uploads/subtitulos-es.vtt">
  </video>
  <figcaption>Video con subtítulos en español.</figcaption>
</figure>
```

### Estructura

```
diegor-accesibilidad/
├── diegor-accesibilidad.php      # Arranque del plugin
├── includes/
│   ├── class-da-settings.php     # Ajustes y pantalla de administración
│   ├── class-da-metabox.php      # Caja «Accesibilidad auditiva» y shortcode
│   └── class-da-frontend.php     # Recursos públicos, subtítulos e iframes
├── views/
│   ├── admin-settings.php
│   ├── admin-metabox.php
│   └── toolbar.php               # Marcado del widget
├── assets/
│   ├── css/da-toolbar.css
│   └── js/da-toolbar.js          # Sin dependencias
├── uninstall.php
└── readme.txt
```

### Importante

Esta barra complementa, pero no reemplaza, un sitio accesible. El cumplimiento de WCAG y de la NTC 5854 también depende del contenido: textos alternativos en las imágenes, subtítulos reales en los videos, contraste suficiente, formularios con etiquetas y navegación por teclado.

### Historial de cambios

**1.0.2**
- Nuevas posiciones del botón: centro a la derecha y centro a la izquierda.

**1.0.1**
- Firma «Diseñado por Diegor.tech» en la barra, en los ajustes y en los archivos.
- Opción para ocultar el crédito en la barra.

**1.0.0**
- Versión inicial.

---

## Licencia

El código se distribuye bajo la licencia [GPL-2.0-or-later](./LICENSE), la misma de WordPress. La marca Diegor.tech y su diseño pertenecen a Diegor.tech.

Diseñado y desarrollado por **[Diegor.tech](https://diegor.tech/)** · Santiago de Cali, Colombia.
