function clickingOnUserValueSelectsOption(container: HTMLElement) {
    container.querySelectorAll<HTMLInputElement>('.option .user_option').forEach(userOption => {
        userOption.addEventListener('click', () => {
            const checkbox = userOption.parentElement.querySelector<HTMLInputElement>('input[type="checkbox"], input[type="radio"]');
            if (null === checkbox) {
                return;
            }
            checkbox.checked = true;

            const input = userOption.querySelector<HTMLInputElement>('input[type="text"]');
            if (null === input) {
                return;
            }
            input.focus();
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
                .querySelectorAll<HTMLInputElement>('tbody td[data-option="' + index + '"] input')
                .forEach(input => {
                    input.checked = true;
                })
        });
    })
}

function oversizedTableDisplayScrollNote(container: HTMLElement) {
    const table = container.querySelector('table');
    const scrollNote = container.querySelector('.note_scroll');

    if (null === table || null === scrollNote) {
        return;
    }

    const checkSize = () => {
        scrollNote.classList.toggle('hidden', table.clientWidth <= table.parentElement.clientWidth)
    };

    checkSize();
    (new ResizeObserver(checkSize)).observe(table.parentElement);
}

function resettingCurrentStepClearsForm(container: HTMLElement) {
    container.querySelector<HTMLInputElement>('input[type="reset"').addEventListener('click', (resetElement) => {
        container.querySelectorAll('input, textarea, select').forEach((el) => {
            if (el === resetElement.target) {
                return;
            }


            if (el instanceof HTMLInputElement) {
                if (el.checked) {
                    el.removeAttribute('checked');

                    return;
                }

                if (el.type === 'text') {
                    el.removeAttribute('value');
                }

                return;
            }

            if (el instanceof HTMLTextAreaElement) {
                el.innerText = "";

                return;
            }

            if (el instanceof HTMLSelectElement) {
                el.selectedIndex = -1;
                el.value = "";
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const survey = document.querySelector<HTMLElement>('*[data-survey]');

    if (null === survey) {
        return;
    }

    clickingOnUserValueSelectsOption(survey);
    clickingOnTableCellSelectsOption(survey);
    clickingOnTableHeaderSelectsAllOfColumn(survey);
    oversizedTableDisplayScrollNote(survey);
    resettingCurrentStepClearsForm(survey);
})
