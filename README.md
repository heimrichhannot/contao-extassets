# Contao Extassets

Create your own css & js groups and add them to your contao theme layouts.

## General features
- Backend Module for external css
- Backend Module for external js 
- Add multiple CSS & JS groups to contao layout 
- Bootstrap framework support (for css by default, enable within js group)
- Font-Awesome added by default (availability of all variables and mixins)
- Elegant Icons can be added (availability of all variables and mixins)
- Css file caching for production mode (disable byPassCache in contao settings)

## External CSS

### Features
- Complete lesscss support, automatically compile all your less files within a external css group to css
- Observer folders (recursive) within your external css groups
- Add multiple custom variable files, to overwrite for example bootstrap variables.less (like @brand-primary)
- make use of all bootstrap mixins and variables within your own less files (See: http://getbootstrap.com/customize/#less-variables)
- bootstrap print.css support
- Internet Explorer 6-9 - 4096 css-selector handling (Internet Explorer 6 - 9 has only a maximum of 4096 css-selectors possible per file. Extassets make usage of https://github.com/zweilove/css_splitter ans solve this problem by splitting aggregated files into parts.)
- all files within $GLOBALS['TL_USER_CSS'] will be parsed within external css groups

### Installation

#### Contao 4.0

1. Install via composer

```
composer require heimrichhannot/contao-extassets
```

2. Add the following to lines to the `$bundles` array in your `app/AppKernel.php` 

```
/**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            …
            new ContaoModuleBundle('extassets', $this->getRootDir()),
            new ContaoModuleBundle('haste_plus', $this->getRootDir()),
        ];

        …
    }
```

3. Clear app chache
 
```
bin/console cache:clear -e prod
```



### Hooks

#### addCustomAssets

Attach custom fonts or css libraries to extassets combiner. 

```
// config.php
$GLOBALS['TL_HOOKS']['addCustomAssets'][] = array('MyClass', 'addCustomAssetsHook');


// MyClass.php

public function addCustomAssetsHook(\Less_Parser $objLess, $arrData, \ExtAssets\ExtCssCombiner $objCombiner)
{
    // example: add custom less variables to your css group to provide acces to mixins or variables in your external css files
    $this->objLess->parseFile('/assets/components/my-library/less/my-variables.less'));
    
    // example: add custom font to your css group
    $objFile = new \File('/assets/components/my-library/css/my-font.css, true);
    $strCss = $objFile->getContent();
    $strCss = str_replace("../fonts", '/assets/components/my-library/'), $strCss); // make font path absolut, mostly required
    $this->objLess->parse($strCss);
}

```

### Font Awesome (http://fontawesome.io/)

Use font-awesome mixins and variables right inside your less files.

```
// my-styles.less
.my-button{
  .fa;
  .fa-lg;
  &:before{
    content: @fa-var-github;
  }
}
```

List of all font-awesome variables, see (https://github.com/heimrichhannot/font-awesome/blob/master/less/variables.less). 


### Elegant Icon Font (http://www.elegantthemes.com/blog/resources/elegant-icon-font)

Use elegant-icon mixins and variables right inside your less files.

```
// my-styles.less
.my-button{
  .ei;
  &:before{
    content: @ei-var-info;
  }
}
```

List of all elegant-icon variables, see (https://github.com/heimrichhannot/elegant-icons/blob/master/less/variables.less). 