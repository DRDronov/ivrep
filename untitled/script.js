function appendAlert(message, type) {
    const wrapper = document.createElement('div');
    wrapper.innerHTML = [
        `<div class="alert alert-${type} alert-dismissible" role="alert">`,
        `   <div>${message}</div>`,
        '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
        '</div>'
    ].join('');

    const alertPlaceholder = document.getElementById('liveAlertPlaceholder');
    alertPlaceholder.append(wrapper);
}//сообщения об ошибке

function updatePagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const button = createPageButton(i);
        if (i === currentPage) {
            button.classList.add('active');
        }
        pagination.appendChild(button);
    }
}

function createPageButton(page) {
    const button = document.createElement('button');
    button.textContent = page;
    button.classList.add('btn', 'btn-secondary', 'mx-1');
    button.addEventListener('click', () => loadFeedbacks(page));
    return button;
}


document.addEventListener('DOMContentLoaded', async function() {


    loadFeedbacks(1);

    const alertPlaceholder = document.getElementById('liveAlertPlaceholder');

    document.getElementById('add-feedback-form').addEventListener('submit', async function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        try {
            const response = await fetch('/api/create', {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error);
            }else{
                appendAlert(data.success, 'success');
            }

            loadFeedbacks(1);
        } catch (error) {
            appendAlert(error.message, 'danger');
        }
    });
    alertPlaceholder.addEventListener('click', function(event) {
        if (event.target.classList.contains('btn-close')) {
            event.target.closest('.alert').remove();
        }
    });
}); //обработчик закрытия сообщений

function loadFeedbacks(page) {
    fetch(`/api/feedbacks/${page}`)
        .then(response => response.json())
        .then(data => {
            const feedbacksList = document.getElementById('feedbacks');
            feedbacksList.innerHTML = '';

            data.feedbacks.forEach(feedback => {
                const li = document.createElement('li', );
                li.classList.add('list-group-item');
                li.innerText = `ID: ${feedback.id} Author: ${feedback.author}
                Content: ${feedback.content}`;
                feedbacksList.appendChild(li);

            });
            updatePagination(data.totalPages, page);
        })
        .catch(error => console.error('Error fetching feedbacks:', error));
}




