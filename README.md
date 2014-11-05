# contao-extassets

Contao external CSS &amp; JS assets groups with bootstrap and font-awesome support

The aim was to make external CSS & Javascript better groupable and attach it to the page layout.

## Features

#### LESS

All CSS files of the group are handled by less.php (https://github.com/oyejorge/less.php) parser and parsed accordingly.

#### Bootstrap

- automatically download the framework (on first page load of the according frontend layout)
- possible to embed on selecte CSS-group
- overwrite all less-variables (variables.less) based on CSS-group
- make use of LESS-Mixins (mixins.less)
- Bootstrap Javascript-Support (Javascript-Group in Layout)

##### Example of usage of bootstrap mixins and variables

Reference a singular variables.less file in your css-group as "variables-src", and overwrite existing bootstrap variables by your own.
Rewritable bootstrap variables can be looked up, at "assets/bootstrap/less/variables.less", after framework has been downloaded automatically. 

Bootstrap mixins and variables can be used easily in your css-code, and redundant css-code will be minimized

```
#main {

	margin-bottom: @grid-gutter-width;

        .carousel-control{
                .transition(opacity 750ms ease-out);
        }

}
```

More: http://getbootstrap.com/ 


#### Font Awesome

- automatically download the framework (on first page load of the according frontend layout)
- possible to embed on selecte CSS-group
- make use of LESS-Mixins & Variables (mixins.less)

##### Example of usage of font-awesome mixins and variables

```
i {
  .fa;
  .fa-lg;
  &:before{
    content: @fa-var-github;
  }
}
```

Font-Awesome variables can be looked up, at "assets/font-awesome/less/variables.less", after framework has been downloaded automatically. 

#### Aggregates css & js files

Enable "Compress scripts" under "System -> Settings -> Global Settings", and css as well as js groups will be aggregated and compressed.

#### Internet Explorer 6-9 - 4096 css-selector handling

Internet Explorer 6 - 9 has only a maximum of 4096 css-selectors possible per file. Extassets make usage of https://github.com/zweilove/css_splitter ans solve this problem by splitting aggregated files into parts.
