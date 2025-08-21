export function tagInput() {

    let tagsContainer = document.querySelector('#tags');
    let tagInput = document.querySelector('#tag-input');
    let hiddenTagInput = document.querySelector("#hidden-tag-input");

    if (!tagInput) return;

    restoreTags();

    tagInput.addEventListener("keydown", function (e) {

        if (e.key == 'Enter') {

            e.preventDefault();

            if (tagsContainer.children.length < 3) {
                addtag(tagInput.value);
                updateHiddenTagField();
            }
            else if (tagsContainer.children.length == 3) {
                addtag(tagInput.value);
                tagInput.disabled = true;
                tagInput.placeholder = 'Tags limit reached';
                tagInput.classList.add("limit");
                updateHiddenTagField();
            }

        }
    })

    function addtag(tag) {

        tagInput.closest('.input-box').classList.remove('error');

        if(!tag.trim()) return;

        if (tag.trim() !== '') {

            tag = tag.replace(/[^a-zA-Z0-9 ]/g, "");

            let tagMarkup = `  
            <div class="tag" id="tag" >
                <span class="text" id="text">${tag}</span>
                <div class="close" id="close">
                <i class='bx bx-x'></i>
                </div>
            </div>
             `;

            tagsContainer.innerHTML += tagMarkup;
            tagInput.value = '';
        }
    }

    document.addEventListener('click', function (e) {
        if (e.target.parentElement.classList.contains('close')) {
            e.target.parentElement.parentElement.remove();
            tagInput.disabled = false;
            tagInput.placeholder = "Enter the tags of the Post"
            tagInput.classList.remove('limit');
            updateHiddenTagField();
        }
    });

    function getTags() {
        let tags = [];
        tagsContainer.querySelectorAll(".tag .text").forEach(tagEl => tags.push(tagEl.textContent.trim()));
        return tags;
    }

    function updateHiddenTagField() {
        let tags = getTags();
        hiddenTagInput.value = tags.join(',');
    }

    function restoreTags(){
        const tagString = hiddenTagInput.value;
        if(!tagString) return;

        const tags = tagString.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
        tags.forEach(tag => addtag(tag));

        if (tags.length === 4) {
            tagInput.disabled = true;
            tagInput.placeholder = 'Tags limit reached';
            tagInput.classList.add("limit");
        }

    }


}