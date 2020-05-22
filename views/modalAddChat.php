<!-- Modale per Chat -->
<div class="modal fade" id="addChatModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add a New Chat</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="formChat">
                <div class="modal-body">
                    <!-- Tipo di Chat -->
                    <div class="d-flex justify-content-center">
                        <div class="row" id="chatTypeSelection">
                            <label style="padding-right: 5px" class="radio-inline">
                                <input type="radio" name="chatType" value="1" checked> Private chat
                            </label>
                            <label style="padding-right: 5px" class="radio-inline">
                                <input type="radio" name="chatType" value="2"> Group
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="chatType" value="3"> Channel
                            </label>
                        </div>
                    </div>

                    <div style="padding: 3px" id="selectFriends" hidden>
                        <!-- nome gruppo/canale -->
                        <div class="input-group">
                            <div class="input-group-append">
                                <span class="input-group-text">Name</span>
                            </div>
                            <input placeholder="Name" id="inputName" type="text" minlength="1" maxlength="15" name="name" value="" class="form-control">
                        </div>

                        <br>

                        <!-- descrizione -->
                        <div class="input-group">
                            <textarea autocomplete="off" type="text" id="inputDescription" name="description" class="form-control" placeholder="Description"></textarea>
                            <div class="input-group-append">
                                <span class="input-group-text">Description</span>
                            </div>
                        </div>

                        <br>

                        <!-- Immagine -->
                        <div class="form-group" style="display: flex; align-items: end;">
                            <div class="input-group input-file" style="display: flex; align-items: end;" id="picture">
                                <img id="imgPictureGroup" style="height: 70px; width: 70px; margin-right: 20px" src="./utils/imgs/img_avatar.png" />
                                <div class="custom-file mb-3" style="margin-top: 1rem !important;">
                                    <input type="file" class="custom-file-input" name="photo" style="margin-bottom: auto; margin-top: auto" id="pictureInput" placeholder="choose a picture..." accept="img/jpeg, img/png">
                                    <label id="labelInput" class="custom-file-label" for="customFile">Choose photo</label>
                                </div>
                                <span style="margin-bottom: auto; margin-top: auto" class="input-group-btn">
                                    <button type="button" onclick="resetFileInput('imgPictureGroup', './utils/imgs/img_avatar.png')" class="btn btn-danger">Reset</button>
                                </span>
                            </div>
                        </div>

                        <br>
                        <p id="groupLimit" hidden>Massimo 50 membri</p>
                        <select id="selectChats" multiple="multiple" name='users[]'>
                            <?php
                            foreach ($detailsFriends as $friend) {
                                $src = $friend->getPathProfilePicture();
                                echo "<option src='" . $src . "' value='" . $friend->getUserId() . "'>" . $friend->getUsername() . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div style="padding: 3px" id="selectFriend">
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="secret" name="isSecret">
                            <label class="custom-control-label" for="secret">Secret chat - Se attivi l'opzione "chat segreta" i messaggi della chat verranno criptati ulteriormente aumentandone la sicurezza e inoltre verranno eliminati definitivamente non appena visualizzati</label>
                        </div>

                        <select id="selectChat" name='users'>
                            <?php
                            foreach ($detailsFriends as $friend) {
                                $src = $friend->getPathProfilePicture();
                                echo "<option src='" . $src . "' value='" . $friend->getUserId() . "'>" . $friend->getUsername() . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button id="btnAdd" type="submit" class="btn btn-success">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>