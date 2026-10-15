=== Diegor Accesibilidad ===
Contributors: diegortech
Tags: accesibilidad, wcag, lengua de señas, lector de voz, subtítulos
Requires at least: 6.3
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later

Barra de accesibilidad para personas con discapacidad visual y auditiva.

Diseñado y desarrollado por Diegor.tech — https://diegor.tech/

== Descripción ==

Añade un botón flotante con un panel accesible por teclado (Alt + A, Escape para cerrar) y compatible con lectores de pantalla.

Visión:
* Tamaño del texto (80 % a 180 %), funciona aunque el tema use tamaños en píxeles.
* Alto contraste, modo oscuro, colores invertidos y escala de grises.
* Fuente legible, espaciado de texto (WCAG 1.4.12), resaltar enlaces y foco visible.
* Cursor grande, guía de lectura y lupa de texto.
* Detener animaciones (se activa sola si el sistema pide reducir movimiento).
* Lector de voz con la voz en español del navegador: leer página, leer selección, leer al hacer clic, pausa y velocidad.

Audición:
* Mostrar subtítulos en videos HTML5 y de YouTube (prioriza pistas en español y de tipo "captions").
* Subtítulos grandes con fondo sólido.
* Intérprete de Lengua de Señas Colombiana por página, en ventana flotante.
* Transcripción por página, en ventana o dentro del contenido con [da_transcripcion].
* Subtítulos en español activados por defecto en los YouTube incrustados por oEmbed.
* Título accesible automático para los iframes que no lo tienen.

Las preferencias se guardan en el navegador del visitante (localStorage). El plugin no usa cookies, no envía datos a terceros y no carga librerías externas.

== Instalación ==

1. Plugins → Añadir nuevo → Subir plugin → selecciona diegor-accesibilidad.zip → Instalar → Activar.
2. Ajustes → Accesibilidad: elige posición, color y herramientas.
3. En cada página o entrada, usa la caja «Accesibilidad auditiva» para el video del intérprete LSC y la transcripción.

== Importante ==

Esta barra complementa, pero no reemplaza, un sitio accesible: textos alternativos en imágenes, subtítulos reales en los videos, contraste suficiente, formularios con etiquetas y navegación por teclado.

== Changelog ==

= 1.0.2 =
* Nuevas posiciones del botón: centro a la derecha y centro a la izquierda.

= 1.0.1 =
* Firma «Diseñado por Diegor.tech» en la barra, en los ajustes y en los archivos.
* Opción para ocultar el crédito en la barra.

= 1.0.0 =
* Versión inicial.
