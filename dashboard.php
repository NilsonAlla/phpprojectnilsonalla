<div class="row  border-bottom white-bg dashboard-header">

    <div class="col-md-12">

        <div class="card-body">
            <div class="row">
                <div class="col-sm-4">
                    <h4>First Name</h4>
                </div>
                <div id="fname" class="col-sm-6 text-secondary">
                    <?= $_SESSION['firstname'] ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-4">
                    <h4>Last Name</h4>
                </div>
                <div id="lname" class="col-sm-6 text-secondary">
                    <?= $_SESSION['lastname'] ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-4">
                    <h4>E-mail</h4>
                </div>
                <div id="mail" class="col-sm-6 text-secondary">
                    <?= $_SESSION['email'] ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-4">
                    <h4>birthday</h4>
                </div>
                <div id="bday" class="col-sm-6 text-secondary">
                    <?= $_SESSION['birthday'] ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-4">
                    <h4>post</h4>
                </div>
                <div id="post" class="col-sm-6 text-secondary">
                    <?= $_SESSION['post'] ?>
                </div>
            </div>
            <hr>
            <div class="row " style="display: inline">
                <div class="">
                    <button id="edit" class="btn btn-info " target="__blank" onclick="edit()">Edit
                    </button>
                </div>
                <?php
                if (strtoupper($_SESSION['post']) == "ADMIN") {
                    ?>
                    <div class="float-right">
                        <button type="button" id="adduseradmin" name="adduser" class="btn btn-success"
                                data-toggle='modal' data-target='#addusermodal'>
                            Add user
                        </button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div id="ndrysho" class="col-md-12 shtim" style="display: none">
        <div class="card mb-3">
            <div id="ndrysho" class="card-body ndrysho">
                <form id="change" class="m-t" role="form" action="" method="post"
                      enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h3>First Name</h3>
                        </div>
                        <div class="col-sm-9  form-group">
                            <input id="firstname" name="firstname" type="text" class="form-control"
                                   value="<?= $_SESSION['firstname'] ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h3>Last Name</h3>
                        </div>
                        <div class="col-sm-9 text-secondary form-group">
                            <input id="lastname" name="lastname" type="text" class="form-control"
                                   value="<?= $_SESSION['lastname'] ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h3>E-Mail</h3>
                        </div>
                        <div class="col-sm-9 text-secondary form-group">
                            <input id="email" name="email" type="text" class="form-control"
                                   value="<?= $_SESSION['email'] ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h3>Birthday</h3>
                        </div>
                        <div class="col-sm-9 text-secondary form-group">
                            <input id="birthdayuserupdate" name="birthday" type="text" class="form-control"
                                   value="<?php echo $_SESSION['birthday'] ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h3>Photo</h3>
                        </div>
                        <div class="col-sm-9 text-secondary form-group">
                            <input id="photo" name="photo" type="file" class="form-control"
                                   accept="image/*">
                        </div>

                        <input type="hidden" id="photo_path_hidden"  value="<?= $_SESSION['photopath'] ?>">

                    </div>

                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-9 text-secondary">
                            <button type="submit" name="submit" id="submit"
                                    class="btn btn-primary px-4" value="1">Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addusermodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog " role="document">
        <!--Content-->
        <div class="modal-content card card-image" style="background: rgba(8,162,38,0.91);">
            <div class="text-white rgba-stylish-strong py-5 px-5 z-depth-4">
                <!--Header-->
                <form method="post" action="backend/adminbackend.php" id="register-form">
                    <div class="modal-header text-center pb-4">
                        <h3 class="modal-title w-100 white-text font-weight-bold" id="myModalLabel"><strong>Add
                                user</strong></h3>
                        <button type="button" class="close white-text" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!--Body-->
                    <div class="modal-body">
                        <!--Body-->
                        <div class="md-form form-group mb-3">
                            <input type="text" id="firstnameuseradd" style="color: #000"
                                   name="firstname" class="form-control validate ">
                            <label data-error="wrong" data-success="right" for="Form-email5">First
                                name</label>
                        </div>
                        <div class="md-form form-group pb-3">
                            <input type="text" id="lastnameuseradd" style="color: #000"
                                   name="lastname" class="form-control validate ">
                            <label data-error="wrong" data-success="right" for="lastnameuseradd">Last
                                name</label>

                        </div>
                        <div class="md-form form-group pb-3">
                            <input type="text" id="emailuseradd" name="email" style="color: #000"
                                   class="form-control  ">
                            <label data-error="wrong" data-success="right" for="emailuseradd">Email</label>

                        </div>
                        <div class="md-form form-group pb-3">
                            <input type="text" id="birthdayuseradd" style="color: #000"
                                   name="birthday" class="form-control validate " autocomplete="off">
                            <label data-error="wrong" data-success="right" for="birthdayuseradd">Birthday</label>

                        </div>
                        <div class="md-form form-group pb-3">
                            <input type="password" id="password" name="password" style="color: #000"
                                   class="form-control validate ">
                            <label data-error="wrong" data-success="right" for="Form-pass5">Password</label>

                        </div>
                        <div class="md-form form-group pb-3">
                            <input type="password" id="confirm_password"
                                   name="confirmpassword" style="color: #000" class="form-control validate ">
                            <label data-error="wrong" data-success="right" for="Form-pass5">Confirm
                                password</label>

                        </div>


                        <!--Grid row-->
                        <div class="row d-flex align-items-center mb-4">

                            <!--Grid column-->
                            <div class="text-center mb-3 col-md-12">
                                <button type="submit"
                                        class="btn btn-success btn-block btn-rounded z-depth-1">
                                    Add user
                                </button>
                            </div>
                            <!--Grid column-->

                        </div>
                        <!--Grid row-->

                        <!--Grid row-->
                        <div class="row">

                            <!--Grid column-->

                            <!--Grid column-->

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
