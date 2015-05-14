
            $(document).ready(function(){
                $("#countries").change(onSelectChange);
            });
            function onSelectChange(){
                var selected = $("#countries option:selected");
                if(selected.val() != 0)
                {
                    $.post("http://setkia.com/index.php/about/singlecountrycost", {country: selected.val()}, function(xml) {
                        // format result
                        var result = [
                            xml
                        ];
                        // output result
                        $("#countrycost").html(result.join(""));
                    } );
                }
                else
                {
                    $("#countrycost").html("");
                }
            };





