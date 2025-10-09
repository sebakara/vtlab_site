<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$bs->website_title}}</title>
    <link rel="icon" href="{{asset('assets/front/img/'.$bs->favicon)}}">
    <link href="{{asset('assets/admin/css/pagebuilder.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/pb-preset.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/pb-shell.css')}}" rel="stylesheet">
    <script src="{{asset('assets/admin/js/core/jquery.3.2.1.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/plugin/pagebuilder/main.min.js')}}"></script>
    {{-- <script src="{{asset('assets/admin/js/plugin/pagebuilder/tui-image-editor.js')}}"></script> --}}
    <script src="{{asset('assets/admin/js/plugin/pagebuilder/plugins.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.9.2/ckeditor.js"></script>


  </head>
  <body>
    {{-- Loader --}}
    <div class="request-loader">
        <img src="{{asset('assets/admin/img/loader.gif')}}" alt="">
    </div>
    {{-- Toast Alert --}}
    <div id="snackbar">Success!</div>
    {{-- Loader --}}
    <div id="gjs" style="height:0px; overflow:hidden;">

    </div>


    <script type="text/javascript">

        function toast(message) {
            $("#snackbar").addClass("show");
            $("#snackbar").html(message);
            let $snackbar = $("#snackbar");
            setTimeout(function(){ $snackbar.removeClass("show"); }, 3000);
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

      var images = [];

      var editor  = grapesjs.init({
        avoidInlineStyle: 1,
        height: '100%',
        container : '#gjs',
        fromElement: 1,
        showOffsets: 1,
        assetManager: {
            storageType: '',
            storeOnChange: true,
            storeAfterUpload: true,
            upload: "{{url('assets/front/img/pagebuilder')}}", //for temporary storage
            assets: [],
            uploadFile: function(e) {
                $(".request-loader").addClass("show");
                var files = e.dataTransfer ? e.dataTransfer.files : e.target.files;
                var formData = new FormData();
                for (var i in files) {
                    formData.append('files[]', files[i]) //containing all the selected images from local
                }

                $.ajax({
                    url: "{{route('admin.pb.upload')}}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    crossDomain: true,
                    dataType: 'json',
                    mimeType: "multipart/form-data",
                    processData: false,
                    success: function(result) {
                        $(".request-loader").removeClass("show");
                        $("#snackbar").css("background-color", "#5cb85c");
                        toast("Image uploaded successfully");
                        editor.AssetManager.add(result['data']); //adding images to asset
                    }
                });
            },
        },
        selectorManager: { componentFirst: true },
        styleManager: { clearProperties: 1 },
        domComponents: { storeWrapper: 1 },
        plugins: [
          'grapesjs-lory-slider',
          'grapesjs-tabs',
          'grapesjs-custom-code',
          'grapesjs-touch',
          'grapesjs-parser-postcss',
          'grapesjs-tui-image-editor',
          'grapesjs-typed',
          'grapesjs-style-bg',
          'gjs-preset-webpage',
          'gjs-plugin-ckeditor',
          'gjs-component-countdown'
        ],
        pluginsOpts: {
          'grapesjs-lory-slider': {
            sliderBlock: {
              category: 'Extra'
            }
          },
          'gjs-plugin-ckeditor': {
            position: 'center',
            options: {
              language: "{{$lang->code}}",
              extraPlugins: 'sharedspace,justify,colorbutton,panelbutton,font,bidi',
              contentsLangDirection: "{{$lang->rtl == 1 ? 'rtl' : 'ltr'}}"
            }
          },
          'grapesjs-tabs': {
            tabsBlock: {
              category: 'Extra'
            }
          },
          'grapesjs-tui-image-editor': {
            // config: {
            //     includeUI: {
            //         initMenu: 'filter',
            //     },
            // },
            // icons: {
            //     'menu.normalIcon.path': '../icon-d.svg',
            //     'menu.activeIcon.path': '../icon-b.svg',
            //     'menu.disabledIcon.path': '../icon-a.svg',
            //     'menu.hoverIcon.path': '../icon-c.svg',
            //     'submenu.normalIcon.path': '../icon-d.svg',
            //     'submenu.activeIcon.path': '../icon-c.svg',
            // },
            onApply: (imageEditor, imageModel) => {
                $(".request-loader").addClass('show');

                let canvas = document.getElementsByClassName('lower-canvas')[0];
                let base_64 = canvas.toDataURL();

                let formData = new FormData();
                formData.append('base_64', base_64);

                $.ajax({
                    url: "{{route('admin.pb.tui.upload')}}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    crossDomain: true,
                    dataType: 'json',
                    mimeType: "multipart/form-data",
                    processData: false,
                    success: function(result) {
                        $(".request-loader").removeClass("show");
                        $("#snackbar").css("background-color", "#5cb85c");
                        toast('Image Edited & Saved in Asset Manager!');
                        editor.AssetManager.add(result['data']); //adding images to asset
                        // Hide TUI image editor
                        $(".gjs-mdl-btn-close").trigger("click");
                        editor.getSelected().set('src', result['data'][0].src);
                    }
                });
            }
          },
          'grapesjs-typed': {
            block: {
              category: 'Extra',
              content: {
                type: 'typed',
                'type-speed': 40,
                strings: [
                  'Text row one',
                  'Text row two',
                  'Text row three',
                ],
              }
            }
          },
          'gjs-preset-webpage': {
            modalImportTitle: 'Import Template',
            modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
            modalImportContent: function(editor) {
              return editor.getHtml() + '<style>'+editor.getCss()+'</style>'
            },
            filestackOpts: null, //{ key: 'AYmqZc2e8RLGLE7TGkX3Hz' },
            aviaryOpts: false,
            blocksBasicOpts: { flexGrid: 1 },
            customStyleManager: [{
              name: 'General',
              buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
              properties:[{
                  name: 'Alignment',
                  property: 'float',
                  type: 'radio',
                  defaults: 'none',
                  list: [
                    { value: 'none', className: 'fa fa-times'},
                    { value: 'left', className: 'fa fa-align-left'},
                    { value: 'right', className: 'fa fa-align-right'}
                  ],
                },
                { property: 'position', type: 'select'}
              ],
            },{
                name: 'Dimension',
                open: false,
                buildProps: ['width', 'flex-width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
                properties: [{
                  id: 'flex-width',
                  type: 'integer',
                  name: 'Width',
                  units: ['px', '%'],
                  property: 'flex-basis',
                  toRequire: 1,
                },{
                  property: 'margin',
                  properties:[
                    { name: 'Top', property: 'margin-top'},
                    { name: 'Right', property: 'margin-right'},
                    { name: 'Bottom', property: 'margin-bottom'},
                    { name: 'Left', property: 'margin-left'}
                  ],
                },{
                  property  : 'padding',
                  properties:[
                    { name: 'Top', property: 'padding-top'},
                    { name: 'Right', property: 'padding-right'},
                    { name: 'Bottom', property: 'padding-bottom'},
                    { name: 'Left', property: 'padding-left'}
                  ],
                }],
              },{
                name: 'Typography',
                open: false,
                buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
                properties:[
                  { name: 'Font', property: 'font-family'},
                  { name: 'Weight', property: 'font-weight'},
                  { name:  'Font color', property: 'color'},
                  {
                    property: 'text-align',
                    type: 'radio',
                    defaults: 'left',
                    list: [
                      { value : 'left',  name : 'Left',    className: 'fa fa-align-left'},
                      { value : 'center',  name : 'Center',  className: 'fa fa-align-center' },
                      { value : 'right',   name : 'Right',   className: 'fa fa-align-right'},
                      { value : 'justify', name : 'Justify',   className: 'fa fa-align-justify'}
                    ],
                  },{
                    property: 'text-decoration',
                    type: 'radio',
                    defaults: 'none',
                    list: [
                      { value: 'none', name: 'None', className: 'fa fa-times'},
                      { value: 'underline', name: 'underline', className: 'fa fa-underline' },
                      { value: 'line-through', name: 'Line-through', className: 'fa fa-strikethrough'}
                    ],
                  },{
                    property: 'text-shadow',
                    properties: [
                      { name: 'X position', property: 'text-shadow-h'},
                      { name: 'Y position', property: 'text-shadow-v'},
                      { name: 'Blur', property: 'text-shadow-blur'},
                      { name: 'Color', property: 'text-shadow-color'}
                    ],
                }],
              },{
                name: 'Decorations',
                open: false,
                buildProps: ['opacity', 'border-radius', 'border', 'box-shadow', 'background-bg'],
                properties: [{
                  type: 'slider',
                  property: 'opacity',
                  defaults: 1,
                  step: 0.01,
                  max: 1,
                  min:0,
                },{
                  property: 'border-radius',
                  properties  : [
                    { name: 'Top', property: 'border-top-left-radius'},
                    { name: 'Right', property: 'border-top-right-radius'},
                    { name: 'Bottom', property: 'border-bottom-left-radius'},
                    { name: 'Left', property: 'border-bottom-right-radius'}
                  ],
                },{
                  property: 'box-shadow',
                  properties: [
                    { name: 'X position', property: 'box-shadow-h'},
                    { name: 'Y position', property: 'box-shadow-v'},
                    { name: 'Blur', property: 'box-shadow-blur'},
                    { name: 'Spread', property: 'box-shadow-spread'},
                    { name: 'Color', property: 'box-shadow-color'},
                    { name: 'Shadow type', property: 'box-shadow-type'}
                  ],
                },{
                  id: 'background-bg',
                  property: 'background',
                  type: 'bg',
                },],
              },{
                name: 'Extra',
                open: false,
                buildProps: ['transition', 'perspective', 'transform'],
                properties: [{
                  property: 'transition',
                  properties:[
                    { name: 'Property', property: 'transition-property'},
                    { name: 'Duration', property: 'transition-duration'},
                    { name: 'Easing', property: 'transition-timing-function'}
                  ],
                },{
                  property: 'transform',
                  properties:[
                    { name: 'Rotate X', property: 'transform-rotate-x'},
                    { name: 'Rotate Y', property: 'transform-rotate-y'},
                    { name: 'Rotate Z', property: 'transform-rotate-z'},
                    { name: 'Scale X', property: 'transform-scale-x'},
                    { name: 'Scale Y', property: 'transform-scale-y'},
                    { name: 'Scale Z', property: 'transform-scale-z'}
                  ],
                }]
              },{
                name: 'Flex',
                open: false,
                properties: [{
                  name: 'Flex Container',
                  property: 'display',
                  type: 'select',
                  defaults: 'block',
                  list: [
                    { value: 'block', name: 'Disable'},
                    { value: 'flex', name: 'Enable'}
                  ],
                },{
                  name: 'Flex Parent',
                  property: 'label-parent-flex',
                  type: 'integer',
                },{
                  name      : 'Direction',
                  property  : 'flex-direction',
                  type    : 'radio',
                  defaults  : 'row',
                  list    : [{
                            value   : 'row',
                            name    : 'Row',
                            className : 'icons-flex icon-dir-row',
                            title   : 'Row',
                          },{
                            value   : 'row-reverse',
                            name    : 'Row reverse',
                            className : 'icons-flex icon-dir-row-rev',
                            title   : 'Row reverse',
                          },{
                            value   : 'column',
                            name    : 'Column',
                            title   : 'Column',
                            className : 'icons-flex icon-dir-col',
                          },{
                            value   : 'column-reverse',
                            name    : 'Column reverse',
                            title   : 'Column reverse',
                            className : 'icons-flex icon-dir-col-rev',
                          }],
                },{
                  name      : 'Justify',
                  property  : 'justify-content',
                  type    : 'radio',
                  defaults  : 'flex-start',
                  list    : [{
                            value   : 'flex-start',
                            className : 'icons-flex icon-just-start',
                            title   : 'Start',
                          },{
                            value   : 'flex-end',
                            title    : 'End',
                            className : 'icons-flex icon-just-end',
                          },{
                            value   : 'space-between',
                            title    : 'Space between',
                            className : 'icons-flex icon-just-sp-bet',
                          },{
                            value   : 'space-around',
                            title    : 'Space around',
                            className : 'icons-flex icon-just-sp-ar',
                          },{
                            value   : 'center',
                            title    : 'Center',
                            className : 'icons-flex icon-just-sp-cent',
                          }],
                },{
                  name      : 'Align',
                  property  : 'align-items',
                  type    : 'radio',
                  defaults  : 'center',
                  list    : [{
                            value   : 'flex-start',
                            title    : 'Start',
                            className : 'icons-flex icon-al-start',
                          },{
                            value   : 'flex-end',
                            title    : 'End',
                            className : 'icons-flex icon-al-end',
                          },{
                            value   : 'stretch',
                            title    : 'Stretch',
                            className : 'icons-flex icon-al-str',
                          },{
                            value   : 'center',
                            title    : 'Center',
                            className : 'icons-flex icon-al-center',
                          }],
                },{
                  name: 'Flex Children',
                  property: 'label-parent-flex',
                  type: 'integer',
                },{
                  name:     'Order',
                  property:   'order',
                  type:     'integer',
                  defaults :  0,
                  min: 0
                },{
                  name    : 'Flex',
                  property  : 'flex',
                  type    : 'composite',
                  properties  : [{
                          name:     'Grow',
                          property:   'flex-grow',
                          type:     'integer',
                          defaults :  0,
                          min: 0
                        },{
                          name:     'Shrink',
                          property:   'flex-shrink',
                          type:     'integer',
                          defaults :  0,
                          min: 0
                        },{
                          name:     'Basis',
                          property:   'flex-basis',
                          type:     'integer',
                          units:    ['px','%',''],
                          unit: '',
                          defaults :  'auto',
                        }],
                },{
                  name      : 'Align',
                  property  : 'align-self',
                  type      : 'radio',
                  defaults  : 'auto',
                  list    : [{
                            value   : 'auto',
                            name    : 'Auto',
                          },{
                            value   : 'flex-start',
                            title    : 'Start',
                            className : 'icons-flex icon-al-start',
                          },{
                            value   : 'flex-end',
                            title    : 'End',
                            className : 'icons-flex icon-al-end',
                          },{
                            value   : 'stretch',
                            title    : 'Stretch',
                            className : 'icons-flex icon-al-str',
                          },{
                            value   : 'center',
                            title    : 'Center',
                            className : 'icons-flex icon-al-center',
                          }],
                }]
              }
            ],
          },
        },
        canvas: {
            styles: [
                "{{asset('assets/front/css/bootstrap.min.css')}}",
                "{{asset('assets/front/css/plugin.min.css')}}",
                "{{asset('assets/front/css/common-style.css')}}",
                @if($version == 'default' || $version == 'dark')
                    "{{asset('assets/front/css/style.css')}}",
                @endif
                @if($version == 'gym')
                    "{{asset('assets/front/css/gym-style.css')}}",
                @endif
                @if($version == 'car')
                    "{{asset('assets/front/css/car-style.css')}}",
                @endif
                @if($version == 'cleaning')
                    "{{asset('assets/front/css/cleaning-style.css')}}",
                @endif
                @if($version == 'construction')
                    "{{asset('assets/front/css/construction-style.css')}}",
                @endif
                @if($version == 'logistic')
                    "{{asset('assets/front/css/logistic-style.css')}}",
                @endif
                @if($version == 'lawyer')
                    "{{asset('assets/front/css/lawyer-style.css')}}",
                @endif
                @if($version == 'ecommerce')
                    "{{asset('assets/front/css/ecommerce-style.css')}}",
                @endif
                "{{asset('assets/admin/css/pb-canvas.css?time=' . time())}}",
                "{{url('/')}}/assets/front/css/common-base-color.php?color={{$bs->base_color}}",
                @if($version == 'default' || $version == 'dark')
                    "{{url('/')}}/assets/front/css/base-color.php?color={{$abs->base_color}}&color1={{$abs->secondary_base_color}}",
                @endif
                @if ($version == 'dark')
                    "{{asset('assets/front/css/dark.css')}}",
                    "{{url('/')}}/assets/front/css/dark-base-color.php?color={{$abs->base_color}}",
                @endif
                @if($version == 'gym')
                    "{{url('/')}}/assets/front/css/gym-base-color.php?color={{$abs->base_color}}",
                @endif
                @if($version == 'car')
                    "{{url('/')}}/assets/front/css/car-base-color.php?color={{$abs->base_color}}",
                @endif
                @if($version == 'cleaning')
                    "{{url('/')}}/assets/front/css/cleaning-base-color.php?color={{$abs->base_color}}&color1={{$abs->secondary_base_color}}",
                @endif
                @if($version == 'construction')
                    "{{url('/')}}/assets/front/css/construction-base-color.php?color={{$abs->base_color}}",
                @endif
                @if($version == 'logistic')
                    "{{url('/')}}/assets/front/css/logistic-base-color.php?color={{$abs->base_color}}&color1={{$abs->secondary_base_color}}",
                @endif
                @if($version == 'lawyer')
                    "{{url('/')}}/assets/front/css/lawyer-base-color.php?color={{$abs->base_color}}",
                @endif
                @if($version == 'ecommerce')
                    "{{url('/')}}/assets/front/css/ecommerce-base-color.php?color={{$abs->base_color}}",
                @endif

                @if($lang->rtl == 1)
                    "{{asset('assets/admin/css/pb-rtl-canvas.css')}}",
                    @if($version == 'default' || $version == 'dark')
                        "{{asset('assets/front/css/rtl.css')}}",
                    @endif
                    @if($version == 'gym')
                        "{{asset('assets/front/css/gym-rtl.css')}}",
                    @endif
                    @if($version == 'car')
                        "{{asset('assets/front/css/car-rtl.css')}}",
                    @endif
                    @if($version == 'cleaning')
                        "{{asset('assets/front/css/cleaning-rtl.css')}}",
                    @endif
                    @if($version == 'construction')
                        "{{asset('assets/front/css/construction-rtl.css')}}",
                    @endif
                    @if($version == 'logistic')
                        "{{asset('assets/front/css/logistic-rtl.css')}}",
                    @endif
                    @if($version == 'lawyer')
                        "{{asset('assets/front/css/lawyer-rtl.css')}}",
                    @endif
                    @if($version == 'ecommerce')
                        "{{asset('assets/front/css/ecommerce-rtl.css')}}",
                    @endif
                @endif
            ],
            scripts: [
                "{{asset('assets/front/js/jquery-3.3.1.min.js')}}",
                // "{{asset('assets/front/js/popper.min.js')}}",
                "{{asset('assets/front/js/bootstrap.min.js')}}",
                "{{asset('assets/admin/js/pb-plugin.min.js')}}",
                "{{asset('assets/admin/js/pb-custom.js')}}",
                @if($version == 'default' || $version == 'dark')
                    "{{asset('assets/admin/js/pb-default-custom.js')}}"
                @endif
                @if($version == 'gym')
                    "{{asset('assets/admin/js/pb-gym-custom.js')}}"
                @endif
                @if($version == 'car')
                    "{{asset('assets/admin/js/pb-car-custom.js')}}"
                @endif
                @if($version == 'cleaning')
                    "{{asset('assets/admin/js/pb-cleaning-custom.js')}}"
                @endif
                @if($version == 'construction')
                    "{{asset('assets/admin/js/pb-construction-custom.js')}}"
                @endif
                @if($version == 'logistic')
                    "{{asset('assets/admin/js/pb-logistic-custom.js')}}"
                @endif
                @if($version == 'lawyer')
                    "{{asset('assets/admin/js/pb-lawyer-custom.js')}}"
                @endif

            ]
        }
      });



      editor.setComponents({!! json_encode($components) !!});
      editor.setStyle({!! json_encode($styles) !!});




        // removing image from assets manager
        editor.on('asset:remove', (asset) => {
            $(".request-loader").addClass('show');

            let fd = new FormData();
            fd.append('path', asset.id);
            $.ajax({
                url: "{{route('admin.pb.remove')}}",
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                success: function(data) {
                    $(".request-loader").removeClass('show');
                    $("#snackbar").css("background-color", "#5cb85c");
                    toast('Image removed successfully!');
                    // console.log(data);
                    // $('iframe').contents().find('.request-loader').remove();
                }
            })
        });

    //  Bootstrap Container Section
    var blockManager = editor.BlockManager;
        blockManager.add('bs-container', {
        label: 'Container',
        attributes: {class:'fa fa-window-maximize'},
        content: {
            components: "<div class='pbcontainer' data-gjs-draggable='true' data-gjs-editable='true' data-gjs-removable='true' data-gjs-propagate='" + ["removable","editable","draggable"] + "'></div>"
        },
        category: 'Basic'
    });

    //  Bootstrap Card Section
    var blockManager = editor.BlockManager;
        blockManager.add('card-1', {
        label: 'Card 1',
        attributes: {class:'fa fa-address-card-o'},
        content: {
            components: `<div class="card">
                <img class="card-img-top" src="https://via.placeholder.com/200X125" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>`
        },
        category: 'Basic'
    });

    //  Bootstrap Card Section
    var blockManager = editor.BlockManager;
        blockManager.add('card-2', {
        label: 'Card 2',
        attributes: {class:'fa fa-address-card-o'},
        content: {
            components: `<div class="card">
                <img class="card-img-top" src="https://via.placeholder.com/200X125" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Cras justo odio</li>
                    <li class="list-group-item">Dapibus ac facilisis in</li>
                    <li class="list-group-item">Vestibulum at eros</li>
                </ul>
                <div class="card-body">
                    <a href="#" class="card-link">Card link</a>
                    <a href="#" class="card-link">Another link</a>
                </div>
            </div>`
        },
        category: 'Basic'
    });

    //  Bootstrap Card Section
    var blockManager = editor.BlockManager;
        blockManager.add('card-3', {
        label: 'Card 3',
        attributes: {class:'fa fa-address-card-o'},
        content: {
            components: `<div class="card text-center">
                <div class="card-header">
                    Featured
                </div>
                <div class="card-body">
                    <h5 class="card-title">Special title treatment</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
                <div class="card-footer text-muted">
                    2 days ago
                </div>
            </div>`
        },
        category: 'Basic'
    });

    //  Bootstrap List Group Section
    var blockManager = editor.BlockManager;
        blockManager.add('list', {
        label: 'List',
        attributes: {class:'fa fa-list'},
        content: {
            components: `<ul class="list-group">
                <li class="list-group-item">Dapibus ac facilisis in</li>
                <li class="list-group-item">Morbi leo risus</li>
                <li class="list-group-item">Porta ac consectetur ac</li>
                <li class="list-group-item">Vestibulum at eros</li>
            </ul>`
        },
        category: 'Basic'
    });

    //  Bootstrap List Group Section
    var blockManager = editor.BlockManager;
        blockManager.add('list-links', {
        label: 'List of Links',
        attributes: {class:'fa fa-list'},
        content: {
            components: `<div class="list-group">
                <a href="#" class="list-group-item list-group-item-action">
                    Cras justo odio
                </a>
                <a href="#" class="list-group-item list-group-item-action">Dapibus ac facilisis in</a>
                <a href="#" class="list-group-item list-group-item-action">Morbi leo risus</a>
                <a href="#" class="list-group-item list-group-item-action">Porta ac consectetur ac</a>
            </div>`
        },
        category: 'Basic'
    });


    //  Bootstrap Card List Section
    var blockManager = editor.BlockManager;
        blockManager.add('card-list', {
        label: 'Card List',
        attributes: {class:'fa fa-list'},
        content: {
            components: `<ul class="list-unstyled">
                <li class="media align-items-center my-4">
                    <img class="mr-3" src="https://via.placeholder.com/150X150" alt="Generic placeholder image">
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">List-based media object</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis.
                    </div>
                </li>
                <li class="media align-items-center my-4">
                    <img class="mr-3" src="https://via.placeholder.com/150X150" alt="Generic placeholder image">
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">List-based media object</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis.
                    </div>
                </li>
            </ul>`
        },
        category: 'Basic'
    });


    //  Bootstrap Button Section
    var blockManager = editor.BlockManager;
        blockManager.add('bs-button', {
        label: 'Button',
        attributes: {class:'fa fa-stop'},
        content: {
            components: `<a href="#" class="btn btn-danger">Button Link</a>`
        },
        category: 'Basic'
    });

    //   Intro Section
    @if(!empty($introsec))
    var blockManager = editor.BlockManager;
        blockManager.add('intro-section', {
        label: 'Intro Section',
        attributes: {class:'fa fa-address-card-o'},
        content: {
            components: `{!! $introsec !!}`
        },
        category: 'Theme Sections',
        render: ({ el }) => {
            const btn = document.createElement('a');
            btn.setAttribute('class', 'block-btn');
            btn.setAttribute('href', '{{route("admin.introsection.index", ["language" => $lang->code])}}');
            btn.setAttribute('target', '_blank');
            btn.innerHTML = 'Manage';
            el.appendChild(btn);
        }
    });
    @endif

    //   Service Categories Section
    @if(!empty($scatsec))
    var blockManager = editor.BlockManager;
        blockManager.add('service-categories', {
        label: 'Featured Service Categories',
        attributes: {class:'fa fa-sitemap'},
        content: {
            components: `{!! $scatsec !!}`
        },
        category: 'Theme Sections',
        render: ({ el }) => {
            const btn = document.createElement('a');
            btn.setAttribute('class', 'block-btn');
            btn.setAttribute('href', '{{route("admin.scategory.index", ["language" => $lang->code])}}');
            btn.setAttribute('target', '_blank');
            btn.innerHTML = 'Manage';
            el.appendChild(btn);
        }
    });
    @endif


    //   Featured Services Section
    @if(!empty($servicesSec))
    var blockManager = editor.BlockManager;
        blockManager.add('services', {
        label: 'Featured Services',
        attributes: {class:'fa fa-cogs'},
        content: {
            components: `{!! $servicesSec !!}`
        },
        category: 'Theme Sections',
        render: ({ el }) => {
            const btn = document.createElement('a');
            btn.setAttribute('class', 'block-btn');
            btn.setAttribute('href', '{{route("admin.service.index", ["language" => $lang->code])}}');
            btn.setAttribute('target', '_blank');
            btn.innerHTML = 'Manage';
            el.appendChild(btn);
        }
    });
    @endif


    //   Approach Section
    @if(!empty($approachsec))
    var blockManager = editor.BlockManager;
        blockManager.add('approach-section', {
        label: 'Approach Section',
        attributes: {class:'fa fa-list'},
        content: {
            components: `{!! $approachsec !!}`
        },
        category: 'Theme Sections',
        render: ({ el }) => {
            const btn = document.createElement('a');
            btn.setAttribute('class', 'block-btn');
            btn.setAttribute('href', '{{route("admin.approach.index", ["language" => $lang->code])}}');
            btn.setAttribute('target', '_blank');
            btn.innerHTML = 'Manage';
            el.appendChild(btn);
        }
    });
    @endif


    //   Featured Portfolios Section
    @if(!empty($portfoliosSec))
    var blockManager = editor.BlockManager;
        blockManager.add('portfolios', {
        label: 'Featured Portfolios',
        attributes: {class:'fa fa-briefcase'},
        content: {
            components: `{!! $portfoliosSec !!}`,
        },
        category: 'Theme Sections',
        render: ({ el }) => {
            const btn = document.createElement('a');
            btn.setAttribute('class', 'block-btn');
            btn.setAttribute('href', '{{route("admin.portfolio.index", ["language" => $lang->code])}}');
            btn.setAttribute('target', '_blank');
            btn.innerHTML = 'Manage';
            el.appendChild(btn);
        }
    });
    @endif


    //   Featured Team Section
    @if(!empty($teamSec))
    var blockManager = editor.BlockManager;
        blockManager.add('members', {
        label: 'Team',
        attributes: {class:'fa fa-users'},
        content: {
            components: `{!! $teamSec !!}`
        },
        category: 'Theme Sections',
        render: ({ el }) => {
            const btn = document.createElement('a');
            btn.setAttribute('class', 'block-btn');
            btn.setAttribute('href', '{{route("admin.member.index", ["language" => $lang->code])}}');
            btn.setAttribute('target', '_blank');
            btn.innerHTML = 'Manage';
            el.appendChild(btn);
        }
    });
    @endif


    //   Statistics Section
    @if(!empty($statisticSec))
    var blockManager = editor.BlockManager;
        blockManager.add('statistics', {
        label: 'Statistics',
        attributes: {class:'fa fa-globe'},
        content: {
            components: `{!! $statisticSec !!}`
        },
        category: 'Theme Sections',
        render: ({ el }) => {
            const btn = document.createElement('a');
            btn.setAttribute('class', 'block-btn');
            btn.setAttribute('href', '{{route("admin.statistics.index", ["language" => $lang->code])}}');
            btn.setAttribute('target', '_blank');
            btn.innerHTML = 'Manage';
            el.appendChild(btn);
        }
    });
    @endif


    //   FAQ Section
    @if(!empty($faqSec))
    var blockManager = editor.BlockManager;
        blockManager.add('faq', {
            label: 'FAQ Section',
            attributes: {class:'fa fa-question'},
            content: {
                components: `{!! $faqSec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.faq.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


    //   Testimonial Section
    @if(!empty($testimonialSec))
    var blockManager = editor.BlockManager;
        blockManager.add('testimonial', {
            label: 'Testimonials',
            attributes: {class:'fa fa-commenting-o'},
            content: {
                components: `{!! $testimonialSec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.testimonial.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


    //   Package Section
    @if(!empty($packageSec))
    var blockManager = editor.BlockManager;
        blockManager.add('package', {
            label: 'Featured Packages',
            attributes: {class:'fa fa-usd'},
            content: {
                components: `{!! $packageSec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.package.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


    //   Blogs Section
    @if(!empty($blogSec))
    var blockManager = editor.BlockManager;
        blockManager.add('blog', {
            label: 'Latest Blogs',
            attributes: {class:'fa fa-newspaper-o'},
            content: {
                components: `{!! $blogSec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.blog.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


    //   CTA Section
    @if(!empty($ctaSec))
    var blockManager = editor.BlockManager;
        blockManager.add('cta', {
            label: 'Call to Action',
            attributes: {class:'fa fa-phone'},
            content: {
                components: `{!! $ctaSec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.cta.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


    //   Partners Section
    @if(!empty($partnerSec))
    var blockManager = editor.BlockManager;
        blockManager.add('partners', {
            label: 'Partners',
            attributes: {class:'fa fa-handshake-o'},
            content: {
                components: `{!! $partnerSec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.partner.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


    //   Featured Product Categories Section
    @if(!empty($pcatsec))
    var blockManager = editor.BlockManager;
        blockManager.add('featured-product-categories', {
            label: 'Featured Product Categories',
            attributes: {class:'fa fa-list'},
            content: {
                components: `{!! $pcatsec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.category.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


    //   Featured Product Categories Section
    @if(!empty($fprodsec))
    var blockManager = editor.BlockManager;
        blockManager.add('featured-new-products', {
            label: 'Featured / New Products',
            attributes: {class:'fa fa-gift'},
            content: {
                components: `{!! $fprodsec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.product.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


    // Home Product Categories Section
    @if(!empty($hcatsec))
    var blockManager = editor.BlockManager;
        blockManager.add('home-cat-products', {
            label: 'Product Categories in Home',
            attributes: {class:'fa fa-list'},
            content: {
                components: `{!! $hcatsec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.category.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


    // Newsletter Section
    @if(!empty($newsletterSec))
    var blockManager = editor.BlockManager;
        blockManager.add('newsletter-section', {
            label: 'Newsletter Section',
            attributes: {class:'fa fa-envelope-o'},
            content: {
                components: `{!! $newsletterSec !!}`
            },
            category: 'Theme Sections',
            render: ({ el }) => {
                const btn = document.createElement('a');
                btn.setAttribute('class', 'block-btn');
                btn.setAttribute('href', '{{route("admin.footer.index", ["language" => $lang->code])}}');
                btn.setAttribute('target', '_blank');
                btn.innerHTML = 'Manage';
                el.appendChild(btn);
            }
    });
    @endif


      editor.I18n.addMessages({
        en: {
          styleManager: {
            properties: {
              'background-repeat': 'Repeat',
              'background-position': 'Position',
              'background-attachment': 'Attachment',
              'background-size': 'Size',
            }
          },
        }
      });

      var pn = editor.Panels;
      var modal = editor.Modal;
      var cmdm = editor.Commands;
      cmdm.add('canvas-clear', function() {
        if(confirm('Areeee you sure to clean the canvas?')) {
          var comps = editor.DomComponents.clear();
          setTimeout(function(){ localStorage.clear()}, 0)
        }
      });
      cmdm.add('set-device-desktop', {
        run: function(ed) { ed.setDevice('Desktop') },
        stop: function() {},
      });
      cmdm.add('set-device-tablet', {
        run: function(ed) { ed.setDevice('Tablet') },
        stop: function() {},
      });
      cmdm.add('set-device-mobile', {
        run: function(ed) { ed.setDevice('Mobile portrait') },
        stop: function() {},
      });



      // Add info command
      var mdlClass = 'gjs-mdl-dialog-sm';
      var infoContainer = document.getElementById('info-panel');
      cmdm.add('open-info', function() {
        var mdlDialog = document.querySelector('.gjs-mdl-dialog');
        mdlDialog.className += ' ' + mdlClass;
        infoContainer.style.display = 'block';
        modal.setTitle('About this demo');
        modal.setContent(infoContainer);
        modal.open();
        modal.getModel().once('change:open', function() {
          mdlDialog.className = mdlDialog.className.replace(mdlClass, '');
        })
      });
      pn.addButton('options', {
        id: 'open-info',
        className: 'fa fa-question-circle',
        command: function() { editor.runCommand('open-info') },
        attributes: {
          'title': 'About',
          'data-tooltip-pos': 'bottom',
        },
      });


      // Add and beautify tooltips
      [['sw-visibility', 'Show Borders'], ['preview', 'Preview'], ['fullscreen', 'Fullscreen'],
       ['export-template', 'Export'], ['undo', 'Undo'], ['redo', 'Redo'],
       ['gjs-open-import-webpage', 'Import'], ['canvas-clear', 'Clear canvas']]
      .forEach(function(item) {
        pn.getButton('options', item[0]).set('attributes', {title: item[1], 'data-tooltip-pos': 'bottom'});
      });
      [['open-sm', 'Style Manager'], ['open-layers', 'Layers'], ['open-blocks', 'Blocks']]
      .forEach(function(item) {
        pn.getButton('views', item[0]).set('attributes', {title: item[1], 'data-tooltip-pos': 'bottom'});
      });
      var titles = document.querySelectorAll('*[title]');

      for (var i = 0; i < titles.length; i++) {
        var el = titles[i];
        var title = el.getAttribute('title');
        title = title ? title.trim(): '';
        if(!title)
          break;
        el.setAttribute('data-tooltip', title);
        el.setAttribute('title', '');
      }

      // Show borders by default
      pn.getButton('options', 'sw-visibility').set('active', 1);


      //   add save button in button panel
      var pnm = editor.Panels;
      pnm.addButton('options', [ { id: 'save-database', className: 'fa fa-floppy-o', command: 'save-database', attributes: {title: 'Save to database'} } ]);

      // save content to database
      cmdm.add('save-database', {
          run: function (em, sender) {
              $(".request-loader").addClass("show");
              sender.set('active', true);

            var components = JSON.stringify(editor.getComponents());
              var styles = JSON.stringify(editor.getStyle());

              var html = editor.getHtml();
              var css = editor.getCss();

              let fd = new FormData();
              fd.append('type', "{{request()->input('type')}}");
              fd.append('id', "{{$id}}");
              fd.append('components', components);
              fd.append('styles', styles);
              fd.append('html', html);
              fd.append('css', css);

              $.ajax({
                url: "{{route('admin.pagebuilder.save')}}",
                type: 'POST',
                data: fd,
                contentType: false,
                processData: false,
                success: function(data) {
                    $(".request-loader").removeClass("show");
                    $("#snackbar").css("background-color", "#5cb85c");
                    toast('Content updated successfully!');
                }
              });
           },
       });

    </script>

  </body>
</html>
