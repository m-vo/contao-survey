import PublishMutationHelper from "./PublishMutationHelper";

function handlePublishStateChanges() {
    const scope = document.querySelector<HTMLElement>('.tl_listing_container');
    if (null === scope) {
        return;
    }

    new PublishMutationHelper(scope).watch();
}

document.addEventListener('DOMContentLoaded', () => {
    handlePublishStateChanges();
})
