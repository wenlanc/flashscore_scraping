'use strict';
// Class definition
var datatable;
var KTDatatableInvoice = function() {
    // Private functions
    var validator;
    var demo = function() {

        datatable = $('.kt-datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                // type : 'local',
                // source : dataJSONArray,
                source: {

                    read: {
                        url: base_url+"bill/ajaxInvoiceList",
                        map: function(raw) {

                            var dataSet = raw;
                            if (typeof raw.data !== 'undefined') {
                                dataSet = raw.data;
                            }
                            return dataSet;
                        },
                    },

                },
                pageSize: 20, // display 20 records per page
                //serverPaging: true,
                //serverFiltering: true,
                //serverSorting: true,
                
            },

            // layout definition
            layout: {
                scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                footer: false, // display/hide footer

                class : 'table-striped myProductTable',
                customScrollbar : false,
            },
            // toolbar
            toolbar: {
                // toolbar placement can be at top or bottom or both top and bottom repeated
                placement: [],

                // toolbar items
                items: {
                    // pagination
                    // pagination: {
                    //     // page size select
                    //     pageSizeSelect: [5, 10, 20, 30, 50], // display dropdown to select pagination size. -1 is used for "ALl" option
                    // },
                },
                icons: {
                    sort: {
                        asc: 'la la-arrow-up',
                        desc: 'la la-arrow-down'
                    },
                    rowDetail: {
                        expand: 'fa fa-caret-down',
                        collapse: 'fa fa-caret-right'
                    },
                }
            },

            rows: {
                autoHide: false,
                afterTemplate: function (row, data, index) {
                    // if(index) return ;
                    var cel = $(":first", row);

                    cel.on('click', function(){
                        var span = $(":first", cel);
                        span = $(":first", span);
                        if(span.hasClass('la-star-o')) {
                            span.removeClass('la-star-o');
                            span.addClass('la-star');
                        } else {
                            span.removeClass('la-star');
                            span.addClass('la-star-o');
                        }
                    });
                }
            },

            // column sorting
            sortable: true,
            pagination: true,
            // columns definition
            columns: [{
                field: 'id',
                title: '#',
                sortable: false,
                width: 20,
                selector: {
                    class: 'kt-checkbox--solid '
                },
                textAlign: 'center',

                
            }, {
                    field: 'invoice',
                    title: `Invoice`,
                    width: 120,
                    textAlign: 'center',

                }, {
                    field: 'amount',
                    title: `Amount`,
                    width: 90,
                    textAlign: 'center',

                }, {
                    field: 'issued',
                    title: `Issued`,
                    width: 110,
                    textAlign: 'center',
                }, 

                {
                    field: 'due',
                    title: `Due`,
                    width: 110,
                    textAlign: 'center',
                },
                
                {
                    field: 'status',
                    title: `Status`,
                    width: 90,
                    textAlign: 'center',
                    template: function(data) {
                        if(data.status == 1)
                        {
                            return '<span class="text-green">Paid </span>';
                        } else {
                            return '<span class=""> Not Paid </span>';
                        }
                    },
                }, 
                {
                    field: 'pdf',
                    title: 'PDF',
                    sortable: false,
                    width: 140,
                    autoHide: false,
                    // template: function(data) {
                    //     return '<a target="_blank" href = "'+base_url+'invoice/generateInvoicePdf/'+data.id+'" ><span class="viewsample">View </span></a>';
                    // },
                    locked: {right: 'xl'}
                },
                
            ],
        });

        datatable.on(
            'kt-datatable--on-check kt-datatable--on-uncheck kt-datatable--on-layout-updated',
            function(e) {
                var checkedNodes = datatable.rows('.kt-datatable__row--active').nodes();
                var count = checkedNodes.length;
            });

        $('#kt_modal_fetch_id').on('click', function(e) {
                var ids = datatable.rows('.kt-datatable__row--active').
                nodes().
                find('.kt-checkbox--single > [type="checkbox"]').
                map(function(i, chk) {
                    return $(chk).val();
                });
                var c = document.createDocumentFragment();
                for (var i = 0; i < ids.length; i++) {
                    var li = document.createElement('li');
                    li.setAttribute('data-id', ids[i]);
                    li.innerHTML = 'Selected record ID: ' + ids[i];
                    c.appendChild(li);
                }
                $(e.target).find('.kt-datatable_selected_ids').append(c);
            });

    };

    var initValidation = function () {
        validator =   $( "#downloadInvoiceForm" ).validate({
            // define validation rules
            rules: {
            },
          
            //display error alert on form submit  
            invalidHandler: function(event, validator) {             
            },

            submitHandler: function (form) {
              // // submit the form
              var records = [];
              datatable.rows('.kt-datatable__row--active').
                nodes().
                find('.kt-checkbox--single > [type="checkbox"]').
                map(function(i, chk) {
                    records.push($(chk).val());
                });
                ;
            console.log(records);

            if(records.length) {
                var form$ = $("#downloadInvoiceForm");
                form$.append("<input type='hidden' name='order_ids' value='" + JSON.stringify(records) + "' />");
                $( "#downloadInvoiceForm" ).get(0).submit();
            }

          }
        });   

    }

    return {
        // Public functions
        init: function() {
            // init dmeo
            demo();
            initValidation();
        },

    };
}();


jQuery(document).ready(function() {
    KTDatatableInvoice.init();

});