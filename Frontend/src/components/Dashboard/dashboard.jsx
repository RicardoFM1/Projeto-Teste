import { useEffect, useState } from "react";
import Api from "../../API/api";
import { Button, Card, Stack } from "react-bootstrap";
import { CiEdit } from "react-icons/ci";
import { MdDelete } from "react-icons/md";
import DadosTable from "../Table/table";
import { toast } from "react-toastify";
import style from "./dashboard.module.css";
import UsuarioModal from "../Modais/Usuario/modalUsuario";
import ModalDeletar from "../Modais/Deletar/modalDeletar";

function Dashboard() {
  const [usuarios, setUsuarios] = useState([]);
  const [dashboard, setDashboard] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [showModalDeletar, setShowModalDeletar] = useState(false);
  const [deletando, setDeletando] = useState(false);
  const [dadosForm, setDadosForm] = useState([]);
 

  const buscarUsuarios = async () => {
    try {
      const res = await Api.get("/usuario");

      setUsuarios(res.data.dados);
    } catch (err) {
      toast.error("Erro ao buscar usuários");
      console.log(err);
    }
  };

  const buscarDashboard = async () => {
    try {
      const res = await Api.get("/dashboard");
      console.log(res.data.dados, "dados");

      if (res.status === 200) {
        setDashboard(res.data.dados);
      }
    } catch (err) {
      toast.error("Erro ao buscar dashboard");
    }
  };

  useEffect(() => {
    buscarUsuarios();
    buscarDashboard();
  }, []);

  const handleEdit = (row) => {
    setShowModal(true);
    setDadosForm(row);
  };

  const handleDelete = (row) => {
    setShowModalDeletar(true);
    setDadosForm(row);
    setDeletando(true);
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
            onClick={() => handleDelete(row)}
          >
            <MdDelete />
          </Button>
        </div>
      ),
    },
  ];

  const enviarDadosForm = async (dados, editando) => {
    try {
      if (editando) {
        const res = await Api.put(
          `/usuario?email_usuario=${dadosForm?.email}`,
          dados,
        );
        
        if (res.status === 200) {
          toast.success(res.data.mensagem);
          setShowModal(false);
          buscarUsuarios();
          buscarDashboard();
        }
      } else if (!editando) {
        const res = await Api.post("/usuario", dados);
        
        
        if (res.status === 201) {
          toast.success(res.data.mensagem);
          setShowModal(false);
          buscarUsuarios();
          buscarDashboard();
        }
      } 
      
    } catch (err) {
      const erros = err.response?.data?.erros;

      if (erros) {
        Object.values(erros).forEach((msg) => {
          toast.error(msg);
        });
      } else {
        toast.error(err.response?.data?.mensagem || "Erro ao enviar dados");
      }
    }
  };

  const deletar = async () => {
    const res = await Api.delete(
          `/usuario?email_usuario=${dadosForm?.email}`
        );
        
        
        if (res.status === 200) {
          toast.success(res.data.mensagem);
          setShowModalDeletar(false);
          buscarUsuarios();
          buscarDashboard();
        }
  }

  return (
    <>
      <h1>Dashboard</h1>

      <Stack className="mb-3 flex-column flex-md-row" gap={5}>
        <Card>
          <Card.Body className={style.cardBody}>
            Total de convidados: <br />
            <span className={style.cardSpan}>
              <strong>{dashboard?.convidados?.total_convidados}</strong>{" "}
            </span>
          </Card.Body>
        </Card>
        <Card>
          <Card.Body className={style.cardBody}>
            Convidados confirmados: <br />
            <span className={style.cardSpan}>
              <strong>
                {dashboard?.convidados?.convidados_confirmados}
              </strong>{" "}
            </span>
          </Card.Body>
        </Card>
        <Card>
          <Card.Body className={style.cardBody}>
            Convidados não confirmados: <br />
            <span className={style.cardSpan}>
              <strong>
                {dashboard?.convidados?.convidados_nao_confirmados}
              </strong>{" "}
            </span>
          </Card.Body>
        </Card>
        <Card>
          <Card.Body className={style.cardBody}>
            Convidados confirmados: <br />
            <span className={style.cardSpan}>
              <strong>
                {dashboard?.convidados?.convidados_cancelados}
              </strong>{" "}
            </span>
          </Card.Body>
        </Card>
      </Stack>
      <Button
        className="ignorar-fonte-btn mb-3 mt-3"
        onClick={() => {
          setShowModal(true);
          setDadosForm(null)
        }}
      >
        Criar novo
      </Button>
      <DadosTable columns={columns} rows={usuarios} keyField={"id_usuario"} />
      <UsuarioModal
        dados={dadosForm}
        handleClose={() => setShowModal(!showModal)}
        show={showModal}
        onSubmit={enviarDadosForm}
      />
      <ModalDeletar
        deletar={deletar}
        handleClose={() => setShowModalDeletar(!showModalDeletar)}
        show={showModalDeletar}
      />
    </>
  );
}

export default Dashboard;
