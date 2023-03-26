'use strict';
// Class definition
KTDatatableFavorites
var KTDatatableFavorites = function() {
    // Private functions

    var demo = function() {

        var datatable = $('.kt-datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                // type : 'local',
                // source : dataJSONArray,
                source: {

                    read: {
                        url: base_url+'favourite/ajaxFavouriteList',
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
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },

            // layout definition
            layout: {
                scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                footer: false, // display/hide footer

                class : 'table-striped myProductTable ',
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
                        asc: 'flaticon2-sort',
                        desc: 'flaticon2-sort',
                    },
                    rowDetail: {
                        expand: 'fa fa-caret-down',
                        collapse: 'fa fa-caret-right'
                    },
                }
            },

            translate : {
                toolbar : {
                    pagination: {
                        items: {
                           // info: ""
                        }
                    }
                }
            },

            rows: {
                autoHide: false,
                afterTemplate: function (row, data, index) {
                    // if(index) return ;

                    var th = $('th');
                    if(index==0 ) {
                        // for(var i = 1; i < 9; i ++ ) {
                        //     var cel = $(":nth-child("+i+")", th);
                        //     cel.append('<i class = "flaticon2-sort"> </i>');
                        // }
                    }
                    var cel = $(":first", row);

                    cel.on('click', function(){
                        var span = $(":first", cel);
                        span = $(":first", span); 
                        var p_id = data.id;
                        if(span.hasClass('la-star-o')) {

                            $.ajax({
                                type: 'POST',
                                url: base_url + "/home/changeFavourite",
                                data: {
                                    product_id : p_id,
                                    checked: 1
                                },
                                dataType: "json",
                                success: function(resultData) { 
                                    if(resultData){
                                        span.removeClass('la-star-o');
                                        span.addClass('la-star');  
                                    }
                                }
                            });

                        } else {
                            
                            $.ajax({
                                type: 'POST',
                                url: base_url + "home/changeFavourite",
                                data: {
                                    product_id : p_id,
                                    checked: 0
                                },
                                dataType: "json",
                                success: function(resultData) {
                                    if(resultData)
                                    {
                                        span.removeClass('la-star');
                                        span.addClass('la-star-o');
                                    }
                                }
                            });
                        }
                        datatable.reload();
                    });
                }
            },

            // column sorting
            sortable: true,
            pagination: true,
            // columns definition
            columns: [
                {
                    field: '',
                    title: '',
                    sortable: false,
                    width: 20,
                    type: 'number',
                    textAlign: 'center',
                    // locked: {left: 'xl'},
                    template: function(data) {
                        return '<span class="la la-star la-lg"></span>';
                    },

                }, {
                    field: 'Sport',
                    title: `<span>SPORT</span>`,
                    width: 80,
                    // locked: {left: 'xl'},
                }, {
                    field: 'Country',
                    title: 'COUNTRY',
                    textAlign: 'center',

                }, {
                    field: 'Competition',
                    title: 'COMPETITION',
                    textAlign: 'center',
                    //responsive: {
                        //visible: 'md',
                        //hidden: 'lg'
                    //},

                }, {
                    field: 'Season',
                    title: 'SEASON',
                    textAlign: 'center',
                    template: function(data) {  
                        var yearRegex = /(\d{4})/g;
                        var years = data["Season"]?data["Season"].match(yearRegex):"";
                        var season_from = "";
                        var season_to = "";
                        if(years){
                            season_from = years[0];
                            if(years.length > 1)
                            {
                                season_to = years[1];
                                return season_from+"-"+season_to;
                            } else {

                            }
                        } else {
                            
                        }   
                        return season_from;
                    },
                    //responsive: {
                        //visible: 'md',
                        //hidden: 'lg'
                    //},
                    width: 90,
                },
                
                // {
                //     field: 'MatchSummary',
                //     title: 'MATCH&nbspSUMMARY',
                //     width : 170,
                //     textAlign: 'center',
                // }, 
                
                {
                    field: 'GamePlayed',
                    title: 'GAME PLAYED',
                    textAlign: 'center',
                    width : 100

                }, {
                    field: 'LastUpdate',
                    title: 'LAST UPDATE',
                    autoHide: false,
                    type: 'date',
                    textAlign: 'center',
                    width : 110
                    // callback function support for column rendering

                }, {
                    field: 'Price',
                    title: 'PRICE',
                    textAlign: 'center',
                    width: 90,
                }, {
                    field: 'Action',
                    title: '',
                    sortable: false,
                    textAlign: 'center',
                    locked: {right: ['xl','lg']},
                    width:280,
                },
                    
                // {
                //     field: 'ViewSample',
                //     title: '',
                //     sortable: false,
                //     width: 110,
                //     autoHide: false,
                //     textAlign: 'center',
                //     // template: function() {
                //     //     return '<a href = "#"><span class="viewsample">View Sample</span></a>';
                //     // },
                // }, {
                //     field: 'AddCart',
                //     title: '',
                //     sortable: false,
                //     width: 160,
                //     textAlign: 'center',
                //     autoHide: false,
                //     // template: function() {
                //     //     return '<a href = "#" class="btn btn-md btn-addtocart"> Add&nbspto&nbspcart<i class="la la-lg m-0 la-shopping-cart"></i></a>';
                //     // },
                // }
            ],
        });
        
        $('#sportSearch').on('change', function() {
            getLeagueList();
            datatable.setDataSourceParam('query.league_p_id', "")
            datatable.search($(this).val(), 'category_id');
            
        });
        $('#countrySearch').on('change', function() {
            getLeagueList();
            datatable.setDataSourceParam('query.league_p_id', "")
            datatable.search($(this).val(), 'country_id');
        });  
        $('#competitionSearch').on('change', function() {
            datatable.search($(this).val(), 'league_p_id');
        });  
    };

    return {
        // Public functions
        init: function() {
            // init dmeo
            demo();
        },
    };
}();


jQuery(document).ready(function() {
    KTDatatableFavorites.init();
});

