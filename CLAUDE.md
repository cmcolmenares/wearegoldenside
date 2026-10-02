# CLAUDE.md — WearegoldenSide

Documentación de referencia para Claude Code. Contiene estructura del proyecto, convenciones de código, identidad de marca, reglas de contenido y contexto general.

---

## Qué es este proyecto

**WearegoldenSide** es la landing page oficial de GoldenSide, dúo independiente venezolano radicado en Madrid formado por **Aarón Marfil (ASMA)** y **Carlos Dalton (Carlos Colmenares)**. El sitio sirve como presencia digital principal del proyecto musical, incluyendo información sobre el dúo, música, shows, contacto y un press kit.

El proyecto está en una etapa creativa denominada **Underground Era (2026)**: más oscura, honesta y humana. No busca proyectar imagen de éxito, sino mostrar el proceso.

---

## Estructura de archivos

```
wearegoldenside/
├── index.html               — Página principal
├── presskit-preview.html    — Press kit en formato HTML (A4 landscape, 5 páginas)
├── robots.txt               — Configuración para crawlers SEO
├── sitemap.xml              — Mapa del sitio (4 URLs)
├── CLAUDE.md                — Este archivo
├── .claude/
│   ├── launch.json          — Servidor de desarrollo (python3 -m http.server 5173)
│   └── settings.json        — Permisos compartidos de Claude Code
├── css/
│   └── styles.css           — Estilos completos (~1853 líneas, metodología BEM)
├── js/
│   └── main.js              — JavaScript vanilla (~208 líneas)
├── assets/
│   ├── images/              — Fotos de banda, portadas de álbumes, logos, favicon
│   ├── fonts/
│   │   └── progress_3/      — Fuente personalizada "Progress" (uso personal)
│   └── gif/                 — Video manifiesto (mp4, webm, gif)
├── legal/
│   ├── aviso-legal.html
│   ├── politica-cookies.html
│   └── politica-privacidad.html
└── docs/
    ├── presskit_brief.md    — Especificaciones del press kit
    └── quienesSomos.md      — Texto biográfico del dúo
```

---

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Markup | HTML5 semántico (ARIA, schema.org) |
| Estilos | CSS3 puro, metodología BEM, custom properties |
| Scripts | JavaScript vanilla (ES6+), sin frameworks |
| Tipografía | Google Fonts + fuente local "Progress" |
| Formularios | Formspree (gestión de envíos) |
| Música | Spotify Embed (iframe) |
| Video | YouTube Embed (iframe) |
| Imágenes IA | MCP nanobanana (Gemini Flash Image) |
| Componentes UI | MCP @21st-dev/magic |

No hay bundler, transpilador ni framework de JavaScript. Todo es HTML/CSS/JS estático.

---

## Sistema de diseño

### Paleta de colores (custom properties en `:root`)

```css
--noir:     #0A0A0A   /* fondo negro principal */
--carbon:   #1A1A1A   /* gris oscuro */
--asphalt:  #2D2D2D   /* gris medio */
--concrete: #4A4A4A   /* gris claro */
--smoke:    #8A8A8A   /* gris muted */
--bone:     #E8E4DF   /* beige claro */
--chalk:    #F5F2ED   /* blanco roto */
--blood:    #C41E1E   /* rojo sangre — acento primario */
--ember:    #8B1A1A   /* rojo oscuro */
--gold:     #D4A843   /* dorado — acento secundario, uso sutil */
```

**Regla:** El rojo (`--blood`) es el único color de acento fuerte. El dorado (`--gold`) se usa con moderación. No agregar colores fuera de esta paleta sin justificación explícita.

### Tipografía

| Fuente | Uso |
|--------|-----|
| Playfair Display | Títulos grandes, display |
| EB Garamond | Cuerpo de texto, párrafos |
| IBM Plex Mono | Etiquetas, detalles técnicos, UI small |
| Oswald | Headings secundarios, uppercase |
| Special Elite | Efectos editoriales, texturas tipográficas |
| Progress | Logo y elementos de marca propios |

### Efectos visuales del sistema

- **Grain overlay** — SVG fractal noise animado sobre toda la página
- **Scanlines** — Líneas horizontales animadas, efecto retro
- **Cursor personalizado** — Punto rojo + anillo, solo desktop
- **Glitch effects** — Distorsión en hero section con keyframes
- **Parallax** — En secciones `#about` y `#about-2` (translateY 0.3x)
- **Reveal animations** — Elementos con clase `.reveal` aparecen al hacer scroll (IntersectionObserver, threshold 0.15)
- **Carrusel infinito** — CSS animation 50s linear infinite, se pausa en hover

---

## Estructura del `index.html`

| Sección | ID / selector | Contenido |
|---------|--------------|-----------|
| Header | `header` | Logo, navegación, hamburger menu |
| Hero | `#hero` | Título animado, CTAs, efectos glitch |
| Social bar | `#redes` | Ticker animado con 6 redes sociales |
| Underground is Open | `#underground-open` | Overlay rojo, texto manifiesto |
| Shows | `#shows` | YouTube live + 2 posters de eventos |
| Manifiesto | `#underground` | Cita del manifiesto Underground Era |
| About | `#about` | Bio del dúo, foto de banda (bg) |
| About alt | `#about-2` | Descripción sonora, segunda foto |
| Música | `#musica` | Canción destacada + carrusel de 8 canciones |
| Contacto | `#contacto` | Formulario Formspree + email/social |
| Footer | `footer` | Copyright, links legales, iconos sociales |
| Cookie banner | `.cookie-banner` | Persistido en localStorage |

---

## JavaScript (`main.js`)

Módulos funcionales implementados:

1. **Cursor personalizado** — Solo en `pointer: fine` (desktop). Clases `.cursor-dot` y `.cursor-ring`.
2. **Header scroll** — Añade clase `solid` con backdrop-blur pasados 60px.
3. **Mobile menu** — Toggle con ESC, overflow hidden en body.
4. **Parallax** — Listener pasivo en scroll, solo en `#about` y `#about-2`.
5. **Reveal on scroll** — IntersectionObserver en `.reveal`, agrega clase `visible`.
6. **GIF video** — Autoplay al entrar en viewport, congela en último frame al terminar.
7. **Carrusel** — Clona tarjetas dinámicamente, CSS animation 50s, pausa en hover.
8. **Formulario Formspree** — Async/await, estados loading/success/error, reset al enviar.
9. **Cookie banner** — localStorage key `gs_cookie_consent`, botones accept/reject.

---

## SEO y metadatos

- Idioma: `es_ES`
- OpenGraph y Twitter Cards configurados
- Schema.org: `MusicGroup` y `MusicEvent` (JSON-LD)
- Sitemap en `/sitemap.xml` con 4 URLs (homepage + 3 legales)
- `robots.txt` permite todos los crawlers

---

## Identidad de marca y tono

### Quiénes son

- **Aarón Marfil / ASMA** — Voz, composición, producción
- **Carlos Dalton / Carlos Colmenares** — Guitarra, coros, producción
- Origen: Venezuela. Base actual: Madrid, España.
- Historia previa: banda Los Daltónicos (indie-rock caraqueño, Sala José Félix Ribas, Teatro Teresa Carreño)

### Géneros musicales

Rock electrónico · Indie alternativo · Indietrónica · Pop alternativo

### Underground Era — Concepto central

La máscara roja simboliza la perfección que mostramos hacia afuera. Debajo existe el proceso real: esfuerzo, dudas, errores, miedo, disciplina, aprendizaje.

> "No venimos a mostrar que ya llegamos. Venimos a mostrar el camino."

No es derrotismo. Es proceso, humanidad, tensión estética, búsqueda del lado dorado.

### Tono de comunicación

**Usar:** profesional · editorial · oscuro · sofisticado · humano · directo · artístico · maduro

**Evitar:** sonar a banda emergente · frases de coaching · exageraciones vacías · claims sin fuente · exceso de dramatismo · textos demasiado largos · tono corporativo frío

### One-liner oficial

> GoldenSide es un dúo independiente venezolano radicado en Madrid que mezcla rock electrónico, indie alternativo e indietrónica con una narrativa emocional sobre identidad, vulnerabilidad y transformación.

---

## Canciones del catálogo

| Canción | Descripción breve |
|---------|------------------|
| Demons (Acoustic) | Versión íntima, apertura de la Underground Era |
| Car Radio | — |
| Babe | — |
| Freedom | Premiado en ONED Art / Experimental Film Festival |
| Playing For Keeps | Incluida en campaña de Samsung |
| Mis Demonios | Nuevo catálogo en español |
| New Beginning | Etapa inicial del proyecto |

---

## Highlights / logros

- "Playing For Keeps" — campaña comercial de **Samsung**
- Finalistas y 3er lugar — **Stay Alive Fest Madrid 2026**
- Presentación en **Mercado de Motores Madrid 2026**
- Opening act para **Gira Tomates Fritos** (Sala Nazca Barcelona + Sala Nau Madrid)
- Más de **600.000 reproducciones** acumuladas en Spotify
- "Freedom" premiado en **ONED Art / Experimental Film Festival**
- Nominación en **Premios Pepsi Music**

**Nota:** Siempre usar "finalistas y 3er lugar" al referirse a Stay Alive Fest. No decir solo "3er lugar".

---

## Equipo del proyecto

| Rol | Persona / entidad |
|-----|------------------|
| Dirección creativa | Felipe Trujillo |
| Management España | Artes Búho (Madrid) |
| Management internacional | WAMM (Caracas) |

---

## Presencia digital

| Plataforma | Uso en el sitio |
|-----------|----------------|
| Spotify | Embed del reproductor + link |
| Instagram | Link + ticker |
| YouTube | 2 videos embebidos + link |
| TikTok | Link + ticker |
| Facebook | Link + ticker |
| Twitter/X | Link + ticker |

---

## Press Kit (`presskit-preview.html`)

Documento de 5 páginas en formato A4 landscape (297mm × 210mm). Optimizado para impresión (`@media print`).

| Página | Contenido |
|--------|-----------|
| 1 | Portada: logo, tagline, Press Kit 2026, Underground Era |
| 2 | Manifiesto: "The Underground Is Open" |
| 3 | Quiénes somos: bios + descripción musical |
| 4 | Highlights: video YouTube + 7 logros + links streaming/social |
| 5 | Contacto: equipo, plataformas, press assets disponibles |

El press kit comparte paleta, tipografía y estética con `index.html`.

---

## Páginas legales

Ubicadas en `/legal/`. Todas comparten estructura y estilos con el sitio principal.

- `aviso-legal.html`
- `politica-cookies.html`
- `politica-privacidad.html`

---

## Servidores MCP disponibles (`.mcp.json`)

| Servidor | Comando | Uso |
|---------|---------|-----|
| `@21st-dev/magic` | `npx -y @21st-dev/magic@latest` | Generación y refinado de componentes UI |
| `nanobanana` | `uvx nanobanana-mcp-server@latest` | Generación de imágenes con Gemini Flash |

---

## Convenciones y reglas de desarrollo

### CSS

- Metodología **BEM**: `.bloque__elemento--modificador`
- Usar siempre custom properties de la paleta, nunca valores hardcodeados de color
- No agregar frameworks externos (Bootstrap, Tailwind, etc.)
- Animaciones vía CSS siempre que sea posible; JS solo cuando sea necesario
- Breakpoint principal: `768px` (mobile/desktop)

### JavaScript

- Vanilla JS, sin dependencias externas
- Listeners de scroll/mouse con `{ passive: true }` para rendimiento
- Formularios con estados explícitos: loading, success, error
- Persistencia de UI en `localStorage` (solo cookie banner por ahora)

### HTML

- HTML5 semántico: `<header>`, `<main>`, `<footer>`, `<nav>`, `<section>`, `<article>`
- ARIA labels en todos los elementos interactivos
- `loading="lazy"` en imágenes que no estén above the fold
- Scripts con `defer` al final del body

### Imágenes

- Fotos de banda: `band-photo-1.jpg`, `band-photo-2.jpg`
- Portadas de canciones en `/assets/images/`
- Logo y isotipo disponibles en PNG
- No comprimir ni redimensionar assets sin confirmar

### Contenido

- Idioma principal: **español**
- Tono editorial, oscuro, humano — nunca corporativo
- No usar frases de coaching ni exageraciones sin respaldo
- El término correcto siempre es **GoldenSide** (sin espacio, sin "we are" en el copy)
- El sitio web usa **wearegoldenside.com** como dominio

---

## Ramas Git

| Rama | Propósito |
|------|-----------|
| `main` | Producción |
| `feature/presskit` | Rama activa — desarrollo del press kit |

Commits existentes: `a6f392e` (develop - first commit), `1f55998` (Initial commit).
