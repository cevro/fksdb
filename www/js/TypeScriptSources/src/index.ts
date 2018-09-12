import { fyziklani } from './fyziklani';

type IApp = (element: Element, module: string, component: string, mode: string, rawData: string) => boolean;

class AppCollector {
    private items: IApp[] = [];

    public register(item: IApp) {
        this.items.push(item);
    }

    public run() {
        document.querySelectorAll('.react-root').forEach((element: Element) => {
            const module = element.getAttribute('data-module');
            const component = element.getAttribute('data-component');
            const mode = element.getAttribute('data-mode');
            const rawData = element.getAttribute('data-data');

            for (const index in this.items) {
                if (this.items.hasOwnProperty(index)) {
                    const item = this.items[index];
                    if (item(element, module, component, mode, rawData)) {
                        break;
                    }
                }
            }
            throw new Error('no match type');
        });
    }
}

const app = new AppCollector();

app.register(fyziklani);
app.run();
