import { useEffect, useState } from "react";
import DashboardCard from "../Cards/Dashboard/dashboardCard";
import DataTable from "../Table/datatable";
import Api from "../../API/api";
import { Button } from "react-bootstrap";
import { CiEdit } from "react-icons/ci";
import { MdDelete } from "react-icons/md";

function Dashboard() {
  const [usuarios, setUsuarios] = useState([]);

  const buscarUsuarios = async () => {
    try {
      const res = await Api.get("/usuario");

      setUsuarios(res.data.dados);
    } catch (err) {
      console.log(err);
    }
  };

  useEffect(() => {
    buscarUsuarios();
  }, []);

  const handleEdit = (row) => {
    console.log("Editando", row);
  };

  const handleDelete = (id) => {
    console.log("Excluindo", id);
  };

  const columns = [
    { header: "Nome", accessor: "nome" },
    { header: "Email", accessor: "email" },
    { header: "Cpf", accessor: "cpf" },
    { header: "Cargo", accessor: "cargo" },
    {
      header: "Acoes",
      accessor: "acoes",
      render: (row) => (
        <div className="d-flex gap-2">
          <Button
            className="ignorar-fonte-btn"
            variant="warning"
            size="sm"
            onClick={() => handleEdit(row)}
          >
            <CiEdit />
          </Button>
          <Button
            className="ignorar-fonte-btn"
            variant="danger"
            size="sm"
            onClick={() => handleDelete(row.id_usuario)}
          >
            <MdDelete />
          </Button>
        </div>
      ),
    },
  ];

  return (
    <>
      <h1>Dashboard</h1>
      <DashboardCard />
      <DataTable columns={columns} rows={usuarios} keyField={"id_usuario"} />
    </>
  );
}

export default Dashboard;
