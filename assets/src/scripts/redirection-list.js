/* globals Charcoal */

export class RedirectionList extends Charcoal.Admin.Widget {
    table;
    tableData = [];
    initialData = [];
    selector;

    constructor(data) {
        super(data);

        this.selector = data.selector;

        console.log(data.redirections)

        //define data
        this.tableData = JSON.parse(data.redirections);

        this.initialData = JSON.parse(JSON.stringify(this.tableData));
    }


    init() {
        this.table = new Tabulator(this.selector + ' .js-tabulator', {
            data: this.tableData, //assign data to table
            maxHeight: '100vh',
            autoResize: true,
            layout: 'fitColumns', //fit columns to width of table (optional)
            columns: [ //Define Table Columns
                {
                    field: 'id', visible: false,
                },
                {
                    title: 'Path', field: 'path', editor: 'input', editorParams: {
                        selectContents: true,
                    }
                },
                {
                    title: 'Redirection', field: 'redirect', editor: 'input', editorParams: {
                        selectContents: true,
                    }
                },
                {
                    title: 'Redirect children',
                    field: 'redirectChildren',
                    editor: 'tickCross',
                    formatter: 'tickCross',
                    editorParams: {}
                },
                {
                    headerVisible: false,
                    headerSort: false,
                    width: 150, // not ideal but works just fine for now.
                    cellClick: (e, cell) => {
                        const actions = {
                            'js-delete': () => this.deleteRow(cell.getRow()),
                            'js-clone': () => this.cloneRow(cell.getRow()),
                            'js-add-below': () => this.addRow({}, false, cell.getRow())
                        };

                        Object.keys(actions).forEach((key) => {
                            if (e.target.classList.contains(key)) {
                                actions[key]();
                            }
                        })
                    },
                    formatter: (cell, formatterParams, onRendered) => {
                        return `<div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-danger js-delete" title="Delete"><i class='fa fa-trash' style="pointer-events: none;"></i></button>
<!--                            <button type="button" class="btn btn-secondary js-clone" title="Clone"><i class='fa fa-clone' style="pointer-events: none;"></i></button>-->
<!--                            <button type="button" class="btn btn-success js-add-below" title="Add row under"><i class='fa fa-plus' style="pointer-events: none;"></i></button>-->
                        </div>`
                    }
                }
            ],
        });

        this.registerEvents();
    }

    registerEvents() {
        this.table.on('dataChanged', function (data) {
            //data - the updated table data

            console.log(data);
        });


        // Buttons
        this.element().on(`click.${this.type()}`, '.js-add-row', () => {
            this.addRow();
        });

        this.element().on(`click.${this.type()}`, '.js-update', () => {
            let data = this.table.getData().filter((row) => {
                // Filter out rows that are in the initial data.
                return !this.initialData.find(data => JSON.stringify(data) === JSON.stringify(row));
            });

            //Send data.
            fetch("/admin/redirections/update", {
                method: "POST",
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (response.status !== 200 && response.status !== 202) {
                        // error
                    }

                    return response.json()
                })
                .then(data => {
                    console.log("Request complete! response:", data);

                    if (data.feedbacks) {
                        Charcoal.Admin.feedback(data.feedbacks).dispatch();
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        });
    }

    deleteRow(row) {
        row.delete();
        this.table.redraw();
    }

    cloneRow(row) {
        this.addRow(row.getData(), false, row)
    }

    addRow(data = {}, top = false, position = null) {
        this.table.addRow(data, top, position).then((row) => {
            // Making sure tabulator height is calculated anew
            this.table.redraw();

            // focus first cell
            let cell = row.getCell('path').getElement();
            setTimeout(() => cell.focus(), 10);

            return row;
        })
    }

    destroy() {
        this.element().off(`${this.type()}`);
    }
}

Charcoal.Admin.Redirect_Widget_Redirections_List = RedirectionList;
