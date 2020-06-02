<!-- Modify Profile Modal -->
<div class="modal fade" id="modalUsrSettings" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Modifica Profilo</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="" method="post" id="formProfilo" enctype="multipart/form-data">

                <!-- Token per il CSRF -->
                <input name="token" id="token" value="<?= \utils\WAF::getCSRF() ?>" hidden>

                <div class="modal-body">
                    <!-- Username -->
                    <div class="input-group">
                        <input autocomplete="off" type="text" id="inputUsername" name="username" class="form-control" placeholder="Username" value="<?= $user->getUsername() ?>" minlength="5" maxlength="15" required>
                        <div class="input-group-append">
                            <span class="input-group-text">Username</span>
                        </div>
                    </div>

                    <br>

                    <!-- Password -->
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text">New password</span>
                        </div>
                        <input placeholder="Password" id="inputPassword" type="password" minlength="6" maxlength="15" name="password" value="" class="form-control pwd">
                        <span class="input-group-btn">
                            <button style="height:38px" class="btn btn-default reveal" type="button">
                                <i id="eye" class="fas fa-eye"></i>
                            </button>
                        </span>
                    </div>

                    <br>

                    <!-- mood -->
                    <div class="input-group">
                        <textarea autocomplete="off" type="text" id="inputMood" name="mood" class="form-control" placeholder="Mood"><?= is_null($user->getMood()) ? '' : $user->getMood() ?></textarea>
                        <div class="input-group-append">
                            <span class="input-group-text">Mood</span>
                        </div>
                    </div>

                    <br>

                    <!-- privacy level -->
                    <div class="input-group">
                        <div class="input-group-append">
                            <span class="input-group-text">privacy level</span>
                        </div>
                        <select id="inputPL" name="pl" class="form-control" placeholder="Privacy level" required>
                            <option value="0">Default</option>
                            <option value="1">Normal</option>
                            <option value="2">Restricted</option>
                            <option value="3">Hidden</option>
                        </select>
                    </div>

                    <br>

                    <!-- Immagine Profilo -->
                    <div class="form-group" style="display: flex; align-items: end;">
                        <div class="input-group input-file" style="display: flex; align-items: end;" id="picture">
                            <?php
                            if (is_null($user->getPathProfilePicture())) {
                                echo '<img id="imgPicture" style="height: 70px; width: 70px; margin-right: 20px" src="./utils/imgs/img_avatar.png"/>';
                            } else {
                                echo '<img id="imgPicture" style="height: 70px; width: 70px; margin-right: 20px" src="' . $user->getPathProfilePicture() . '" />';
                            }
                            ?>
                            <div class="custom-file mb-3" style="margin-top: 1rem !important;">
                                <input type="file" name="picture" class="custom-file-input" style="margin-bottom: auto; margin-top: auto" id="pictureInput" placeholder="choose a picture..." accept="img/jpeg, img/png">
                                <label id="labelInput" class="custom-file-label" for="customFile">Choose photo</label>
                            </div>
                            <span style="margin-bottom: auto; margin-top: auto" class="input-group-btn">
                                <button type="button" onclick="resetFileInput('imgPicture', '<?= is_null($user->getPathProfilePicture()) ? './utils/imgs/img_avatar.png' : $user->getPathProfilePicture() ?>')" class="btn btn-danger">Reset</button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="saveProfilo" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>