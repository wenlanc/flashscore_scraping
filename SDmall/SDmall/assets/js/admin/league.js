
'use strict';
// Class definition
var validator;
var LEAGUETABLE = function() {
    // Private functions
    
    var demo = function() {

        var datatable = $('#leagueTable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                // type : 'local',
                // source : dataJSONArray,
                source: {

                    read: {
                        url: base_url+'admin/league/ajaxList',
                        map: function(raw) {

                            var dataSet = raw;
                            if (typeof raw.data !== 'undefined') {

                                dataSet = raw.data;
                            }
                            return dataSet;
                        } , 
                    }, 
                },
                pageSize: 10, // display 20 records per page
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true, 
                
            }, 
            // layout definition
            layout: {
                scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                footer: false, // display/hide footer

                class : 'table-striped myProductTable',
                customScrollbar : false,
                icons: {
                    sort: {asc: 'flaticon2-sort', desc: 'flaticon2-sort'},
                    pagination: {
                        next: 'flaticon2-next',
                        prev: 'flaticon2-back',
                        first: 'flaticon2-fast-back',
                        last: 'flaticon2-fast-next',
                        more: 'flaticon-more-1',
                    },
                    rowDetail: {expand: 'fa fa-caret-down', collapse: 'fa fa-caret-right'},
                },
                spinner: {
                }
            },
            // toolbar
            toolbar: {
                // toolbar placement can be at top or bottom or both top and bottom repeated
                placement: ['bottom'],

                // toolbar items
                items: {
                    // pagination
                    pagination: {
                        // page size select
                        pageSizeSelect: [5, 10, 20, 30, 50], // display dropdown to select pagination size. -1 is used for "ALl" option
                    },
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
            },

            // column sorting
            sortable: true,

            pagination: true,

            search: {
				input: $('#generalSearch'),
               
			},

            // columns definition
            columns: [
                
                /*
                {
                field: 'RunID',
                title: 'Defunct',
                sortable: false,
                width: 60,
                template : function(){

                    return `
                    <span class="kt-switch kt-switch--brand kt-switch--sm">
                        <label>
                        <input type="checkbox" checked="checked" name="">
                        <span></span>
                        </label>
                        </span>
                        `;
                }

            },  
            
            {
                field: 'id',
                title: `ID`,

            },*/
             {
                field: 'league_name',
                title: `NAME`,
            }, {
                field: 'league_link',
                title: `LINK`,
                sortable: true,
            },
            {
                field: 'sport_name',
                title: `SPORTNAME`,
            }, {
                field: 'country',
                title: `Country`,
            }, 
            
            {
                field: 'action',
                title: 'ACTION',
                sortable: false,
                width: 110,
                autoHide: false,
                // template: function(data) {
                //     return `
                      
                //         <a href= "javascript:void(0);" onclick="javascript:editLeague();return false;" class = "dayHead editTable kt-menu__link"><i class="la la-edit la-2x"></i>  </a>
                      
                //       `;
                // },
            }
            
        ],
        });

        $('#filter_sport_id').on('change', function() {
            datatable.search($(this).val(), 'sport_name');
        });

        $('#filter_country_id').on('change', function() {
            datatable.search($(this).val().toLowerCase(), 'country');
        });

        $('#filter_sport_id,#filter_country_id').select2({
            placeholder: "Please select one",
            allowClear: true,
        });

    };

    var validateFunc = function(){
        validator = $( "#formCreateLeague" ).validate({
            // define validation rules
            rules: {
                league_id: {
                    required: true
                },
                sport_id: {
                    required: true
                },
                tourID: {
                    required: true
                },
                league_name: {
                    required: true
                },
                league_link: {
                    required: true
                },
            },
            
            //display error alert on form submit  
            invalidHandler: function(event, validator) {             
            // var alert = $('#kt_form_1_msg');
            // alert.removeClass('kt-hidden').show();
            // KTUtil.scrollTo('#price', -200);
            },
    
            submitHandler: function (form) {
                console.log(form);
                form[0].submit(); // submit the form
            }
        }); 
        
        $( "#formEditLeague" ).validate({
            // define validation rules
            rules: {
                league_id: {
                    required: true
                },
                sport_id: {
                    required: true
                },
                tourID: {
                    required: true
                },
                league_name: {
                    required: true
                },
                league_link: {
                    required: true
                },
            },
            
            //display error alert on form submit  
            invalidHandler: function(event, validator) {             
            // var alert = $('#kt_form_1_msg');
            // alert.removeClass('kt-hidden').show();
            // KTUtil.scrollTo('#price', -200);
            },
    
            submitHandler: function (form) {
                console.log(form);
                form[0].submit(); // submit the form
            }
        });
    }

    return {
        // Public functions
        init: function() {
            // init dmeo
            demo();
            validateFunc();

        },

    };
}();



jQuery(document).ready(function() {
    
    LEAGUETABLE.init();
    $('#addLeague').on('click', function() {
        $('#leagueModal').modal('show');
    });
    //$('#sport_id').select2({
    //    placeholder: "Please select one"
    //});
    $('.kt-select2').select2({
        placeholder: "Please select one"
    });

});

function editLeague( league_data ){
    console.log(league_data);
    $('#formEditLeague select[name="tourID"]').val(league_data.tournament_id).change();
    $('#formEditLeague input[name="league_id"]').val(league_data.id);
    $('#formEditLeague input[name="league_name"]').val(league_data.name);
    $('#formEditLeague input[name="league_link"]').val(league_data.link);

    $('#formEditLeague select[name="sport_id"]').val(league_data.sport_id).change();

    $('#leagueEditModal').modal('show');

}