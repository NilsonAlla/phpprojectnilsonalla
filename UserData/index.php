<?php

if (strtoupper($_SESSION['post']) != "ADMIN") {
    header('Location: http://localhost/nilsi/WD1/forbidden.php');
    exit();
}
?>
<?php
include "DB/database.php";



$queryUser = "SELECT id,firstname,lastname,email FROM user WHERE 1=1";
$userResult = mysqli_query($conn,$queryUser);
$user =array();
while ( $row = mysqli_fetch_assoc($userResult)){
    $user[$row['id']]['firstname']=$row['firstname'];
    $user[$row['id']]['lastname']=$row['lastname'];
    $user[$row['id']]['email']=$row['email'];
}

?>

<div id="" class="gray-bg dashbard-1">
    <div class="row  border-bottom white-bg dashboard-header" style="width: 100%">
        <!--code-->
        <form id="filters" method="post" action="../backend/select2.php">
            <div class="row col-lg-12">

                <div class="col-lg-3">
                    <label for="date">
                        Search by date</label>
                    <input id="datefilter" name="date" type="text" class="form-control" placeholder="Date" autocomplete="off" >
                </div>

                <div class="col-lg-3">
                    <label for="select2name">
                        Search by firstname or lastname</label>

                    <select id="select2name" class="form-control" multiple
                            style="width: 80% " placeholder="name">
                        <optgroup label="First Name">
                            <?php foreach ($user as $key => $value) { ?>
                                <option><?= $value['firstname'] ?></option>
                            <?php } ?>
                        </optgroup>
                        <optgroup label="Last Name">
                            <?php foreach ($user as $keys => $values) { ?>
                                <option><?= $values['lastname'] ?></option>
                            <?php } ?>
                        </optgroup>
                    </select>

                </div>

                <div class="col-lg-3">
                    <label for="select2email" value="" style=" width: 300px;">
                        Search by email </label>
                    <br>
                    <select id="select2email" class="js-example-responsive form-control" multiple
                            style="width: 80%" placeholder="email">
                        <?php foreach ($user as $emailKey => $emailValue) { ?>
                            <option><?= $emailValue['email'] ?></option>
                        <?php } ?>
                    </select>

                </div>
                <div class="col-lg-3" style="margin-top: 30px">

                    <button id="btn" class="btn btn-outline-success my-2 my-sm-0" type="submit">
                        Search
                    </button>
                </div>

            </div>
        </form>
        <div class="row" style="height: 30px; width: 100%">

        </div>
        <div class="row col-lg-12">
            <table id="datatable" class="display" style="width:100% !important;">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th>Birthday</th>
                    <th>Role</th>
                    <th>Action</th>

                </tr>
                </thead>
            </table>
        </div>
        <!--code-->
    </div>
    <?php
    ?>
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


                        <!--Grid row-->
                        <div class="row d-flex align-items-center mb-4">

                            <!--Grid column-->
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

<!--modals-->


<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="backend/adminbackend.php" method="post" id="deleteuser">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete user</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <input type="hidden" id="userdeleteid" name="userdeleteid">
                        <label>First Name:</label>
                        <br>
                        <input type="text" id='firstnamedeletemodal' name="first_name_modal" readonly>
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <br>
                        <input type="text" id='lastnamedeletemodal' name="last_name_modal" readonly>
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Email:</label>
                        <br>
                        <input type="text" id='emaildeletemodal' name="emaildeletemodal" readonly>
                    </div>
                    <br>

                    <label>Birthday:</label>
                    <br>
                    <div class="form-group">
                        <input type="text" id='birthdaydeletemodal' name="birthday_modal" readonly>
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Role:</label>
                        <br>
                        <input id="postdeletemodal" name="postdeletemodal" class="form-control optform "
                               readonly>

                    </div>
                    <br>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="delete" name="delete">Yes</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="backend/adminbackend.php" method="post" id="updateuser">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update user</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card" style="width: 14rem;">
                        <img id="photoEditShowModal" class="card-img-top" src="" alt="There is no photo">

                    </div>
                    <br>
                    <div class="form-group">
                        <input type="hidden" id="usereditid" name="usereditid">
                        <label>First Name:</label>
                        <br>
                        <input type="text" id='first_name_modal' name="first_name_modal">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <br>
                        <input type="text" id='last_name_modal' name="last_name_modal">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Email:</label>
                        <br>
                        <input type="text" id='email_modal' name="email_modal">
                    </div>
                    <br>

                    <label>Birthday:</label>
                    <br>
                    <div class="form-group ">
                        <input class="" type="text" id='birthday_modal' name="birthday_modal">
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Role:</label>
                        <br>
                        <select id="post_modal" name="post_modal" class="form-control optform ">
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <br>
                    <div class="form-group">
                        <label>Photo:</label>
                        <br>
                        <input type="file" id='photo_modal' name="photo_modal">
                    </div>
                    <br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="update" name="update">Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php

?>
</body>
</html>

