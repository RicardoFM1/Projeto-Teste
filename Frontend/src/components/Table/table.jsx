import { Table } from "react-bootstrap"



function DadosTable({ columns, rows, chave }) {
    const temDados = rows && rows.length > 0

    return (
        <Table responsive bordered hover>
            <thead>
                <tr>
                    {columns.map(col => (
                        <th key={col.accessor}>{col.header}</th>
                    ))}
                </tr>
            </thead>
            <tbody>
                {temDados ? (
                    rows.map(row => (
                        <tr key={row[chave]}>

                            {columns.map(col => (
                                <td key={col.accessor}>{col.render ? col.render(row) : row[col.accessor]}</td>
                            ))}
                        </tr>
                    ))
                ): (
                    <td colSpan={columns.length} className="text-center text-mute">
                        Nenhum dado a ser mostrado
                    </td>
                )}
            </tbody>
        </Table>
    )
}


export default DadosTable