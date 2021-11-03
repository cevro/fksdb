import {
    forceLink,
    forceManyBody,
    forceSimulation,
    forceX,
    forceY,
    SimulationLinkDatum,
    SimulationNodeDatum,
} from 'd3-force';
import * as React from 'react';

interface Link extends SimulationLinkDatum<Node> {
    from: number;
    to: number;
    level: number | null;
}

interface Node extends SimulationNodeDatum {
    name: string;
    gender: 'M' | 'F';
}

interface OwnProps {
    data: {
        links: Link[];
        nodes: {
            [key: number]: Node;
        };
    };
}

export default class PersonRelationChart extends React.Component<OwnProps, {}> {
    private simulation = null;

    public componentDidMount() {
        this.simulation.on('tick', () => {
            this.forceUpdate();
        });
    }

    public render() {
        const {data: {links, nodes}} = this.props;
        const simNodes = [];
        for (const key in nodes) {
            if (nodes.hasOwnProperty(key)) {
                simNodes.push(nodes[key]);
            }
        }
        links.forEach((link) => {
            link.source = nodes[link.from];
            link.target = nodes[link.to];
        });
        this.simulation = forceSimulation<Node>(simNodes)
            .force('link', forceLink(links).distance(() => 60))
            .force('charge', forceManyBody().strength(-175))
            .force('x', forceX())
            .force('y', forceY())
            .alphaMin(0);

        const nodesElements = [];

        for (const key in nodes) {
            if (nodes.hasOwnProperty(key)) {
                const person = nodes[key];
                const color = person.gender === 'M' ? '#2196F3' : '#E91E63';
                nodesElements.push(
                    <g fill="currentColor"
                       key={key}
                       strokeLinecap="round"
                       onClick={() => {
                           if (person.fx === 0) {
                               person.fx = null;
                               person.fy = null;
                           } else {
                               person.fx = 0;
                               person.fy = 0;
                           }
                       }}
                       strokeLinejoin="round"
                       transform={'translate(' + person.x + ',' + person.y + ')'}>
                        <circle
                            stroke="none"
                            fill={color}
                            r="7.5"
                        />
                        <text x="8" y="0.31rem" fontSize=".5rem" fontWeight="bold" fill={color}>
                            {person.name}
                        </text>
                    </g>,
                );
            }
        }

        return <div className="row">
            <div className="col-9">
                <svg viewBox="-600 -400 1200 800">
                    <defs>
                        {[0, 1, 2, 3, 4].map((level) => {
                            return <marker
                                key={level}
                                viewBox="-5 0 10 10"
                                id={'arrow-end-' + level}
                                refX="10"
                                refY="5"
                                markerWidth="10"
                                markerHeight="10"
                                orient="auto"
                            >
                                <path
                                    d="M 5 5 L -2 2 L -2 8 z"
                                    fill={this.getColorByMeta(level)}
                                    stroke="none"
                                />
                            </marker>;
                        })}
                        {[0, 1, 4].map((level) => {
                                return <marker
                                    key={level}
                                    viewBox="-5 0 10 10"
                                    id={'arrow-start-' + level}
                                    refX="-10"
                                    refY="5"
                                    markerWidth="10"
                                    markerHeight="10"
                                    orient="auto"
                                >
                                    <path
                                        d="M -5 5 L 2 2 L 2 8 z"
                                        fill={this.getColorByMeta(level)}
                                        stroke="none"
                                    />
                                </marker>;
                            },
                        )}
                    </defs>
                    <g fill="none" strokeWidth="1.5">{links.map((item, index) => {

                        if (item.level === 1 || item.level === 4 || !item.level) {
                            return <path
                                key={index}
                                stroke={this.getColorByMeta(item.level)}
                                strokeDasharray={(item.target.gender === item.source.gender) ? '5,5' : 'none'}
                                d={`M ${item.source.x} ${item.source.y} L ${item.target.x} ${item.target.y}`}
                                markerEnd={`url(#arrow-end-${item.level})`}
                                markerStart={`url(#arrow-start-${item.level})`}/>;
                        }
                        const r = Math.hypot(item.target.x - item.source.x, item.target.y - item.source.y);
                        const rot = Math.atan((item.target.y - item.source.y) / (item.target.x - item.source.x)) * 180 / Math.PI;
                        return <path
                            key={index}
                            stroke={this.getColorByMeta(item.level)}
                            strokeDasharray={(item.target.gender === item.source.gender) ? '5,5' : 'none'}
                            d={`M ${item.source.x} ${item.source.y} A ${r} ${r} ${rot} 0 1 ${item.target.x} ${item.target.y}`}
                            markerEnd={`url(#arrow-end-${item.level})`}/>;
                    })}</g>
                    <g>
                        {nodesElements}
                    </g>
                </svg>
            </div>
        </div>;
    }

    private getColorByMeta(meta: number): string {
        switch (meta) {
            case 1:
                return '#4CAF50';
            case 2:
                return '#FFEB3B';
            case 3:
                return '#FF5722';
            case 4:
                return '#9C27B0';
            case 5:
                return '#2196F3';
            default:
                return '#9E9E9E';
        }
    }
}
