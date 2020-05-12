<div class="modal fade" id="modalSendFile" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload file</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="formUploadFile">
                <div class="modal-body">
                    <img id="fileImg" style="width: 70px; margin-right: 20px" src="" />
                    <span id="fileName"></span>
                    <div id="input"></div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Message (optional):</label>
                        <textarea id="fileText" class="form-control" name="messageText" id="message-text"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="uploadFile" type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>