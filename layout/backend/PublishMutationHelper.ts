export default class PublishMutationHelper {
    private readonly sourceAttribute: string = 'data-haste-ajax-operation-value';
    private readonly targetAttribute: string = 'data-js-record-source';

    private readonly container: HTMLElement;

    constructor(container: HTMLElement) {
        this.container = container;
    }

    public watch(): void {
        const observer = new MutationObserver(this.onMutationsCallback);

        observer.observe(this.container, {
            attributes: true,
            subtree: true
        })
    }

    private findTargetElement(source: Element): Element {
        let el = source;

        while (null !== (el = el.parentElement)) {
            if (this.container === el) {
                return null;
            }

            const target = el.querySelector('*[' + this.targetAttribute + ']');

            if (null !== target) {
                return target;
            }
        }

        return null;
    }

    private onMutationsCallback = mutations => {
        mutations.forEach(mutation => {
            if ('attributes' === mutation.type && this.sourceAttribute === mutation.attributeName) {
                const source: Element = <Element>mutation.target;
                const target = this.findTargetElement(source);

                if (null === target) {
                    return;
                }

                // replace html
                fetch(target.getAttribute(this.targetAttribute))
                    .then(response => response.text())
                    .then(html => {
                        target.innerHTML = html;
                    });
            }
        });
    }
}