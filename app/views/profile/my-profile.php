
<div class="title"><span>My Profile</span></div>

<div class="main-content my-profile">

    
    <div class="profile">
    
        <div class="alert de-active">
             <p class="alert__msg" >Something is done!</p>
              <i class='bx bx-x alert__close'></i> 
        </div>

        <div class="title1">Profile Details</div>

        <form action="" class="update-details" id="update-details" >

            <div class="input-box">
                <label for="">Username</label>
                <input type="text" name="" id="username" placeholder="Enter your username">

                <span class="error-box">Enter your username</span>

            </div>

            <div class="input-box" >
                <label for="">Email</label>
                <input type="email" name="" id="email" placeholder="Enter your email">

                <span class="error-box"></span>

            </div>

            <input type="submit" value="Update Details" class="btn btn--primary btn--profile-update">


        </form>

        <div class="title2">Role</div>

            <form action="" class="update-role" id="update-role" >

                <div class="input-box">
                
                    <label for="">Role</label>

                    <div class="select-wrapper">
                        <select name="" id="user-type-select">
                            <option value="" selected disabled>Select an option</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="boss">Boss</option>
                        </select>
                        <i class='bx bx-chevron-down' ></i>
                    </div>

                    <span class="error-box"></span>

                </div>

                <div class="input-box hidden" id="code-box">
                    <label for="">Admin</label>
                    <input type="text" name="" id="code" placeholder="Enter admin code">
                    <span class="error-box"></span>
                </div>

                <input type="submit" value="Update Role" class="btn btn--primary btn--profile-update">

            </form>


        <div class="title2">Password</div>

        <form action="" class="update-password" id="update-password" >

            <div class="input-box current-pass">
                <label for="password">Current Password</label>
                <input type="password" id="current-password" placeholder="Enter your current password">
                <span class="error-box"></span>

            </div>

            <div class="input-box" >
                <label for="password">New Password</label>
                <input type="password" id="password" placeholder="Enter your new password">
                <span class="error-box"></span>

            </div>

            <div class="input-box" >
                <label for="password">Confirm Password</label>
                <input type="password" id="confirm-password" placeholder="Confirm new password">
                <span class="error-box"></span>

            </div>

            <input type="submit" value="Update Password" class="btn btn--primary btn--profile-update" >


        </form>

    </div>

    <!-- <div class="profile-picture">

        <div class="title">Profile Details</div>

        <img src="assets/images/wallpaperflare.com_wallpaper(1).jpg" alt="">
        
        <form action="" class="update-picture" id="update-picture" >

            <div class="input-box" >
                <label for="Post Picture">Post Picture</label>
                <div class="custom-file-upload" id="custom-file-upload">
                    <input type="file" hidden class="file-upload-input" id="file-upload-input" >
                    <button type="button" class="file-upload-btn" id="file-upload-btn">Browse ...</button>
                    <span class="file-upload-msg" id="file-upload-msg">No File Selected</span>
                </div>
            </div>

            <input type="submit" value="Update Picture" class="submit-btn" >

        </form>

    </div> -->


</div>
