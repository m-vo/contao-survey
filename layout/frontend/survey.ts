function clickingOnUserValueSelectsOption(container: HTMLElement) {
    container.querySelectorAll<HTMLInputElement>('input.user_option').forEach(userInput => {
        userInput.addEventListener('click', () => {
            const input = userInput.parentElement.querySelector<HTMLInputElement>('input[type="checkbox"]');
            if(null === input) {
                return;
            }

            input.checked = true;
        });
    })
}

function clickingOnTableCellSelectsOption(container: HTMLElement) {
    container.querySelectorAll<HTMLInputElement>('td input').forEach(input => {
        input.parentElement.addEventListener('click', () => {
            input.checked = true;
        });
    })
}

function clickingOnTableHeaderSelectsAllOfColumn(container: HTMLElement) {
    container.querySelectorAll<HTMLInputElement>('thead td.option').forEach(header => {
        header.addEventListener('click', () => {
            const index = header.getAttribute('data-option');
            header
                .closest('table')
                .querySelectorAll<HTMLInputElement>('tbody td[data-option="'+ index +'"] input')
                .forEach(input => {
                    input.checked = true;
                })
        });
    })
}

document.addEventListener('DOMContentLoaded', () => {
    const survey = document.querySelector<HTMLElement>('*[data-survey]');

    if (null === survey) {
        return;
    }

    clickingOnUserValueSelectsOption(survey);
    clickingOnTableCellSelectsOption(survey);
    clickingOnTableHeaderSelectsAllOfColumn(survey);
})
