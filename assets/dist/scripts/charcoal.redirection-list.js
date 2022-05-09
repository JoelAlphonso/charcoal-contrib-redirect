/* globals Charcoal, Tabulator */

class RedirectionList extends Charcoal.Admin.Widget {

    defaultRowData() {
       return {
            path: '',
            redirect: '/',
            redirectionCode: 301
        }
    }

    constructor(data) {
        super(data);

        this.table = null;
        this.selector = data.selector;
        this.objectType = data.object_type;

        // define data
        this.tableData = JSON.parse(data.redirections) || [];
        // needed to prevent object reference with edited data.
        this.initialData = JSON.parse(data.redirections) || [];
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
                    title: 'Type',
                    field: 'redirectionCode',
                    editor: 'list',
                    editorParams: {
                        allowEmpty: false,
                        values: {
                            301: 'Permanent (301)',
                            302:'Temporary (302)'
                        },
                        defaultValue: 301,
                        listOnEmpty:true,
                        freetext: false
                    },
                    formatter:"lookup",
                    formatterParams:{
                        301: 'Permanent (301)',
                        302:'Temporary (302)'
                    }
                },
                {
                    title: 'Redirect children',
                    field: 'redirectChildren',
                    editor: 'tickCross',
                    formatter: 'tickCross',
                    editorParams: {},
                    visible: false
                },
                {
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
                        });
                    },
                    formatter: () => {
                        return `<div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-danger js-delete" title="Delete"><i class='fa fa-trash' style="pointer-events: none;"></i></button>
<!--                            <button type="button" class="btn btn-secondary js-clone" title="Clone"><i class='fa fa-clone' style="pointer-events: none;"></i></button>-->
<!--                            <button type="button" class="btn btn-success js-add-below" title="Add row under"><i class='fa fa-plus' style="pointer-events: none;"></i></button>-->
                        </div>`;
                    }
                }
            ],
        });

        this.registerEvents();
    }

    registerEvents() {
        this.table.on('dataChanged', data => {
            //data - the updated table data
            this.toggleUpdateButtonState(data);
        });

        this.table.on('tableBuilt', () => {
            this.element().removeClass('is-loading');
        });

        // Buttons
        this.element().on(`click.${this.type()}`, '.js-add-row', () => {
            this.addRow(this.defaultRowData());
        });

        this.element().on(`click.${this.type()}`, '.js-update', () => {
            let data = this.table.getData().filter((row) => {
                // Filter out rows that are in the initial data.
                return !this.initialData.find(data => JSON.stringify(data) === JSON.stringify(row));
            });

            this.toggleUpdateLoader(true);

            //Send data.
            fetch("/admin/redirections/update", {
                method: "POST",
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
                .then(response => {
                    if (response.status !== 200 && response.status !== 202) ;

                    return response.json();
                })
                .then(data => {
                    console.log("Request complete! response:", data);

                    if (data.feedbacks) {
                        Charcoal.Admin.feedback(data.feedbacks).dispatch();
                    }

                    if (data.redirections) {
                        this.table.setData(data.redirections);
                        this.initialData = JSON.parse(JSON.stringify(data.redirections));

                        this.toggleUpdateButtonState(data.redirections);
                    }

                    this.toggleUpdateLoader(false);
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        });
    }

    toggleUpdateButtonState(data) {
        // change button state based on data comparison with original data.
        this.element()
            .find('.js-update')
            .prop('disabled', JSON.stringify(data) === JSON.stringify(this.initialData));
    }

    deleteRow(row) {
        this.confirm(
            {
                title: 'Delete Row',
                message: 'Are you sure you wish to proceed with deleting this row?',
            },
            () => { // On confirm
                if (!row.getData().id) {
                    row.delete();
                    this.table.redraw();
                    return;
                }

                const data = {
                    obj_type: this.objectType,
                    obj_id: row.getData().id,
                };

                fetch('/admin/object/delete', {
                    method: "POST",
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            row.delete();
                            this.table.redraw();
                        }

                        if (data.feedbacks) {
                            Charcoal.Admin.feedback(data.feedbacks).dispatch();
                        }
                    });
            }
        );
    }

    cloneRow(row) {
        this.addRow(row.getData(), false, row);
    }

    addRow(data = {}, top = false, position = null) {
        this.table.addRow(data, top, position).then((row) => {
            // Making sure tabulator height is calculated anew
            this.table.redraw();

            // focus first cell
            let cell = row.getCell('path').getElement();
            setTimeout(() => cell.focus(), 10);

            return row;
        });
    }

    toggleUpdateLoader(flag)
    {
        const button = this.element().find('.js-update');
        const loader = button.find('.js-loader');

        if (flag) {
            button.children().addClass('d-none');
            loader.removeClass('d-none');
        } else {
            button.children().removeClass('d-none');
            loader.addClass('d-none');
        }
    }

    destroy() {
        this.element().off(`${this.type()}`);
    }
}

Charcoal.Admin.Redirect_Widget_Redirections_List = RedirectionList;

export { RedirectionList };
