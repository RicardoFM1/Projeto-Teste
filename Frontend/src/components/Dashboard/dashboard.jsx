import { useEffect, useState } from "react";
import Api from "../../API/api";
import { Button } from "react-bootstrap";
import { CiEdit } from "react-icons/ci";
import { MdDelete } from "react-icons/md";
import DadosTable from "../Table/table";
import { toast } from "react-toastify";

function Dashboard() {
  const [usuarios, setUsuarios] = useState([]);

  const buscarUsuarios = async () => {
    try {
      const res = await Api.get("/usuario");

      setUsuarios(res.data.dados);
    } catch (err) {
      toast.error('Erro ao buscar usuários', {
        position: "top-right",
        autoClose: 3000,
      });
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
     <DadosTable columns={columns} rows={usuarios} keyField={"id_usuario"} />
    </>
  );
}

export default Dashboard;