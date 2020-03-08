<?php
/**
 * view for admin/bulkLoadImages
 *
 * @since 1.0.10
 * @author Keith Wheatley
 * @package echocms
 */
?>
    <div class='col-lg-8 col-lg-offset-2 col-sm-8 col-sm-offset-2 vertical-space-md'>
        <h4 class='text-center'>Bulk Load Images</h4>
        <p>This process bulk loads images and sets up new related items. Before this process is run the images must be transferred by ftp to the folder 'bulkload' in the cms folder. The images for each new project must be in separate folders. When the process completes successfully, the folders will be moved to the 'loaded' folder within the 'bulkload' folder.</p><p>The folder names are used to provide the date, topic, subtopic and header data to create the new projects and must be in the format: </p> <p>&nbsp;&nbsp;&nbsp;"yyyy-mm-dd<b>_</b>topic<b>_</b>subtopic<b>_</b>header"</p><br/>

        <div class='col-sm-12 text-center'>
            <button class='btn btn-default btn-xs startButton'>start</button>
            <button class='btn btn-default btn-xs stopButton'>cancel</button>
        </div>

        <div class="col-sm-12 marginTop">
            <div class="panel panel-default">
                <div class='panel-body' id='results'>
                </div>
            </div>
        </div>

        <div class="progress col-sm-12">
          <div id="progressor" class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" style="width: 0%">
          </div>
        </div>


    </div>

<script>
    var source;
    $('.startButton').on({
        click: function(){
            source = new EventSource('<?php echo CONFIG_URL; ?>admin/bulkLoadImagesSSE');
            source.addEventListener('message', function(e) {
                var result = JSON.parse( e.data );
                addLine(result.message);
                document.getElementById('progressor').style.width = result.progress + "%";
                document.getElementById('progressor').innerHTML = result.progress + "%";
                if (result.progress == '100') {
                    $('.progress-bar').removeClass('active progress-bar-warning').addClass('progress-bar-success');
                    source.close();
                }
            });
            source.addEventListener('error' , function(e) {
                addLine('EventSource error');
                source.close();
            });
        }
    });

    $('.stopButton').on({
        click: function(){
            source.close();
            document.getElementById('progressor').innerHTML = '';
            document.getElementById('progressor').style.width = "0%";
            addLine('cancelled');
        }
    });

    function addLine(message) {
        var r = document.getElementById('results');
        r.innerHTML += message + '<br>';
        r.scrollTop = r.scrollHeight;
    }
</script>
