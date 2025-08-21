<!-- *** Add Modal *** -->

<div class="modal modal--add" id="add-modal">

    <div class="modal__wrapper">

        <div class="modal__head">
            <span class="modal__title"></span>
            <button data-modal-close class="modal__close-btn">&times;</button>
        </div>

        <div class="modal__body">

            <div class="modal__icon">
                <div class="modal__icon-wrapper">
                    <div class="modal__icon-shape">
                        <i class='bx bx-plus'></i>
                    </div>
                </div>
            </div>

            <div class="modal__heading">
                <p class="modal__message-title">Add</p>
                <p class="modal__message-text"></p>
            </div>

            <div class="alert de-active">
                <span class="alert__msg"></span>
                <i class='bx bx-x alert__close'></i> 
            </div>

            <form action="" class="modal__form" method="post" id="">

                <div class="modal__input-box">
                    <label for="name">Name</label>
                    <input type="text" class="name" id="name" placeholder="Add your name">
                    <span class="error-box">Enter your</span>
                </div>

                <div class="modal__actions">

                    <input type="submit" class="btn btn--modal btn--modal-add-edit" value="Add">

                    <button 
                        type="button" 
                        class="btn btn--modal btn--modal-cancel"
                        data-modal-close
                        >
                        Cancel
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- *** Edit Modal *** -->

<div class="modal modal--edit" id="edit-modal">

    <div class="modal__wrapper">

        <div class="modal__head">
            <span class="modal__title"></span>
            <button data-modal-close class="modal__close-btn">&times;</button>
        </div>

        <div class="modal__body">

            <div class="modal__icon">
                <div class="modal__icon-wrapper">
                    <div class="modal__icon-shape">
                        <i class='bx bxs-pencil'></i>
                    </div>
                </div>
            </div>

            <div class="modal__heading">
                <p class="modal__message-title">Edit</p>
                <p class="modal__message-text"></p>
            </div>

            <div class="alert de-active">
                <span class="alert__msg"></span>
                <i class='bx bx-x alert__close'></i> 
            </div>

            <form action="" class="modal__form" method="post" id="">

                <div class="modal__input-box">
                    <label for="name">Name</label>
                    <input type="text" class="name" id="name" placeholder="">
                    <span class="error-box">Enter your</span>
                </div>

                <div class="modal__actions">

                    <input type="submit" class="btn btn--modal btn--modal-add-edit" value="Edit">

                    <button 
                        type="button" 
                        class="btn btn--modal btn--modal-cancel"
                        data-modal-close
                        >
                        Cancel
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- *** Delete Modal *** -->

<div class="modal modal--delete" id="del-modal">

    <div class="modal__wrapper">

        <div class="modal__head">
            <span class="modal__title"></span>
            <button data-modal-close class="modal__close-btn">&times;</button>
        </div>

        <div class="modal__body">

            <div class="modal__icon">
                <div class="modal__icon-wrapper">
                    <div class="modal__icon-shape">
                        <i class="fa-solid fa-exclamation"></i>
                    </div>
                </div>
            </div>

            <div class="modal__heading">
                <p class="modal__message-title">Delete This ?</p>
                <p class="modal__message-text">This will be permanently deleted</p>
            </div>

            <div class="alert de-active">
                <span class="alert__msg"></span>
                <i class='bx bx-x alert__close'></i> 
            </div>

            <form action="" class="modal__form " method="post" id="">

                <div class="modal__input-box">
                    <input type="hidden" class="id-field" name="delete-id">
                    <input type="hidden" class="url-field" name="redirect">
                </div>

                <div class="modal__actions">

                    <input type="submit" class="btn btn--modal btn--modal-delete" value="Delete">

                    <button 
                        type="button" 
                        class="btn btn--modal btn--modal-cancel"
                        data-modal-close
                        >
                        Cancel
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<div class="overlay" id="overlay"></div>