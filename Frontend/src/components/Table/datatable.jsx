import Table from "react-bootstrap/Table";

function DataTable({ columns, rows }) {
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
          {rows.map(row => (
            <tr key={row.id}>
              {columns.map(col => (
                <td key={col.accessor}>{row[col.accessor]}</td>
              ))}
            </tr>
          ))}
        </tbody>
      </Table>
  );
}

// Aprender a mapear dados e colocar na tabela.
// Customizar a tabela

export default DataTable;
