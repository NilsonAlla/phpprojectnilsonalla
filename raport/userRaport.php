<?php
if (strtoupper($_SESSION['post']) != "ADMIN") {
    header('Location: forbidden.php');
    exit();
}
?>
<div class="card ">
    <div class="card-body ">
        <table id="datatable" class="display" style="width: 100% !important;">
            <thead>
            <tr>
                <th></th>
                <th>Nr</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>First Date</th>
                <th>Last Date</th>
                <th>Total whithin 8 hours</th>
                <th>Total whithout 8 hours</th>
                <th>Total hours</th>
            </tr>
            </thead>

        </table>
    </div>
</div>
<!--modals-->
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

                        <div class="row d-flex align-items-center mb-4">

                            <div class="text-center mb-3 col-md-12">
                                <button type="submit"
                                        class="btn btn-success btn-block btn-rounded z-depth-1">
                                    Add user
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



