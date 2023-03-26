'use strict';
// Class definition
KTDatatableDownloads
var KTDatatableDownloads = function() {
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
                        url: base_url + "download/ajaxDownloadList",
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
                layout: [],
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
            columns: [
                {
                    field: '',
                    title: '',
                    sortable: false,
                    width: 20,
                   // type: 'number',

                    // locked: {left: 'xl'},
                    template: function() {
                        //return '<span class="la la-star-o la-lg"></span>';
                    },

                }, {
                    field: 'Sport',
                    title: `<span>SPORT</span>`,
                    width : 90
                    // locked: {left: 'xl'},
                }, {
                    field: 'Country',
                    title: 'COUNTRY</i>',

                }, {
                    field: 'Competition',
                    title: 'COMPETITION',
                    //responsive: {
                    //    visible: 'md',
                    //    hidden: 'lg'
                    //},

                }, {
                    field: 'Season',
                    title: 'SEASON',
                    width : 130,
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
                    //    visible: 'md',
                    //    hidden: 'lg'
                    //}
                }, 
                
                // {
                //     field: 'MatchSummary',
                //     title: 'MATCH&nbspSUMMARY',
                //     width : 170,

                // }, 
                
                {
                    field: 'GamePlayed',
                    title: 'GAME PLAYED',
                    textAlign: 'center',
                    width : 100,
                }, {
                    field: 'LastUpdate',
                    title: 'LAST UPDATE',
                    autoHide: false,
                    type: 'date',
                    textAlign: 'center',

                    // callback function support for column rendering

                }, {
                    field: 'Price',
                    title: 'PRICE',
                    textAlign: 'center',

                }, 
                
                {
                    field: 'Action',
                    title: '',
                    sortable: false,
                    textAlign: 'center',
                    locked: {right: ['xl','lg']},
                    width:280,
                }
                
                // {
                //     field: 'ViewSample',
                //     title: '',
                //     sortable: false,
                //     width: 110,
                //     autoHide: false,
                //     // template: function() {
                //     //     return '<a href = "#"><span class="viewsample">View Sample</span></a>';
                //     // },
                // }, {
                //     field: 'Download',
                //     title: '',
                //     sortable: false,
                //     width: 160,
                //     autoHide: false,
                //     // template: function() {
                //     //     return '<a href = "#" class="btn btn-md btn-addtocart"> Download&nbsp<i class="flaticon2-download-2"></i></a>';
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
    KTDatatableDownloads.init();

});
