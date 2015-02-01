less-modules
============

A set of LESS modules commonly used in my projects.

LESS modules can be loaded in your LESS styles as a complete package:

```css
@import "less-modules/less/less-modules";
```

… or as standalone modules (the complete list):

```css
@import "less-modules/less/caret";
@import "less-modules/less/grid";
@import "less-modules/less/responsive-background";
@import "less-modules/less/sticky-footer";
```

## Caret
```css
.caret();
```

Draws an arrow using only CSS properties. No images required.

### Options
| Option     | Default value      | Description                      |
|------------|--------------------|----------------------------------|
| **color**  | #000               | Caret color                      |
| **width**  | 1em                | Caret width                      |
| **height** | *width value*      | Caret height                     |

### Basic Example
```css
.dropdown-handle {
    .caret();
}
```

### Customized Caret
```css
.next {
    .caret-right(#fff, 1.2em, 1em);
}
```

## Grid
```css
.grid();
```

Creates a justified grid of inline blocks.

There is no need to bother with absolute units or calculating margins. Grid item width is defined in percents and
margins are automatically adjusted to fit the container so everything works, no matter what size the container is.

### Options
| Option                | Default value      | Description                                                         |
|-----------------------|--------------------|---------------------------------------------------------------------|
| **grid-item-width**   | 22%                | Width of a grid item; any unit can be used                          |
| **root-font-size**    | 16px               | Root font size in pixels used for `rem` fallback in legacy browsers |
| **line-height**       | 1.5                | Line height restored in grid items                                  |

### Basic Example
```css
.gallery {
    .grid();
}
```

### Adjusting Number of Items per Line
Grid item width is 22% by default (which results in 4 items per line) and can be easily adjusted on first usage:

```css
.gallery {
    .grid(33%);
}
```

Default column sets can also be applied:

```css
.gallery {
    .grid();
    .grid-cols-3();

    @media screen and (min-width: 768px) {
        .grid-cols-5();
    }
}
```

### Root em Fallback
To avoid unwanted spacing of grid items which would break the grid, grid's font size is set to 0 and then restored back
to root font size in items. `rem` unit is used together with `px` fallback which can be defined as the second parameter
to make the fallback match your design.

```css
.gallery {
    .grid(33%, 13px);
}
```
    
Credits: http://www.barrelny.com/blog/text-align-justify-and-rwd

## Responsive Background
```css
.responsive-background();
```

Generates CSS code with different backgrounds for phone (default), tablet, and desktop. Default breakpoint values
and image naming conventions are compatible with Bootstrap 3.

Mobile first and retina optimized.

Assumes that following images exist:

    ../images/[ image ]_xs@2x.jpg (retina phone)
    ../images/[ image ]_sm@2x.jpg (retina tablet)
    ../images/[ image ]_lg@2x.jpg (retina desktop)

### Options
| Option                | Default value      | Description                                                         |
|-----------------------|--------------------|---------------------------------------------------------------------|
| **image**             | default            | This is the place to put your image base name                       |
| **tablet**            | 768px              | Tablet breakpoint                                                   |
| **desktop**           | 992px              | Desktop breakpoint                                                  |
| **path**              | ../images          | Path to images, relative to final CSS                               |

### Basic Example
```css
section {
    .responsive-background('bg_section');
}
```

#### Resulting CSS:
```css
section {
    background-image: url('../images/bg_section_xs@2x.jpg');
}

@media screen and (min-width: 768px) {
    section {
        background-image: url('../images/bg_section_sm@2x.jpg');
    }
}

@media screen and (min-width: 992px) {
    section {
        background-image: url('../images/bg_section_lg@2x.jpg');
    }
}
```

### Customizing Breakpoint Values and Image Path
```css
.header {
    .responsive-background('backgrounds/bg_header', 800px, 1024px, '../assets/images');
}
```

## Sticky Footer
```css
.sticky-footer();
```

Makes page footer stick to the bottom of the screen.

### Options
| Option              | Default value      | Description                      |
|---------------------|--------------------|----------------------------------|
| **footer-height**   | 10em               | Height of sticky footer          |

### Basic Example
Apply sticky footer on default markup:

```html
<html>
    …
    <body>
        …
        <footer>
            …
        </footer>
    </body>
</html>
```

LESS:
```css
.sticky-footer();
```

### Adjusting Footer Height
Footer height value is `10em` by default and can be easily adjusted to fit your design:

```css
.sticky-footer(200px);
```

### Advanced Use
Sticky footer components can be also applied independently on markup:

```css
.my-wrapper {
    .sticky-footer-wrapper();
}

.my-wrapper-inner {
    .sticky-footer-sub-wrapper(5em);
}

.my-footer {
    .sticky-footer-footer(5em);
}
```

Credits: http://mystrd.at/modern-clean-css-sticky-footer/
