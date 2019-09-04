import * as React from 'react';
import * as ReactDOM from 'react-dom';

export interface NetteActionsData {
    [name: string]: string;
}

export class NetteActions {
    private readonly data: NetteActionsData;

    constructor(data: NetteActionsData) {
        this.data = data;
    }

    public getAction(key: string): string {
        return this.data[key];
    }
}

export type App = (element: Element, module: string, component: string, mode: string, rawData: string, actions: NetteActions) => boolean;

class AppsCollector {
    private items: App[] = [];

    public register(item: App): void {
        this.items.push(item);
    }

    public run(): void {

        document.querySelectorAll('.react-root,[data-react-root]').forEach((element: Element) => {
            // if (element.className.match(/.*react-element-served.*/)) {
            if (element.getAttribute('data-served')) {
                return;
            }
            const module = element.getAttribute('data-module');
            const component = element.getAttribute('data-component');
            const mode = element.getAttribute('data-mode');
            const rawData = element.getAttribute('data-data');
            const actionsData = JSON.parse(element.getAttribute('data-actions'));
            const actions = new NetteActions(actionsData);
            for (const index in this.items) {
                if (this.items.hasOwnProperty(index)) {
                    const item = this.items[index];
                    if (item(element, module, component, mode, rawData, actions)) {
                        element.setAttribute('data-served', '1');
                        // element.className += ' react-element-served';
                        return;
                    }
                }
            }
            throw new Error('no match type');
        });
    }
}

export const appsCollector = new AppsCollector();

export interface RegisterProps<T> {
    actions: NetteActions;
    data: T;
}

export function autoRegister<D>(
    reactComponent: React.ComponentClass<RegisterProps<D>>,
    moduleName: string,
    componentName: string,
): App {

    return (element, module, component, mode, rawData, actions) => {
        if (module !== moduleName) {
            return false;
        }
        if (component !== componentName) {
            return false;
        }

        const data = JSON.parse(rawData);

        ReactDOM.render(React.createElement(reactComponent, {data, actions}), element);

        return true;
    };
}
