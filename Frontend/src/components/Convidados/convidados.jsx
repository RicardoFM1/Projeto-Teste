import { use, useEffect, useState } from "react";
import Api from "../../API/api";
import { Button } from "react-bootstrap";
import { CiEdit } from "react-icons/ci";
import { MdDelete } from "react-icons/md";
import { IoMdAddCircleOutline } from "react-icons/io";
import DadosTable from "../Table/table";
import { toast } from "react-toastify";
import ConvidadoModal from "../Modais/Convidado/modalConvidado";
import ModalDeletar from "../Modais/Deletar/modalDeletar";

function Convidados() {
  const [convidados, setConvidados] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [showModalDeletar, setShowModalDeletar] = useState(false);
  const [dadosForm, setDadosForm] = useState(null);
  const [editando, setEditando] = useState(false);
  

  const buscarConvidados = async () => {
    try {
      const res = await Api.get("/convidado");

      setConvidados(res.data.dados);
      console.log(res.data.dados);
    } catch (err) {
      toast.error("Erro ao buscar convidados");
      console.log(err);
    }
  };

  useEffect(() => {
    buscarConvidados();
  }, []);

  const handleEdit = (row) => {
    
    setDadosForm(row);
    setShowModal(true);
  };

  const handleDelete = async (row) => {
    
    setDadosForm(row);
    setShowModalDeletar(true);
  };

  const columns = [
    { header: "Id", accessor: "id_convidado" },
    { header: "Nome", accessor: "nome" },
    { header: "Sobrenome", accessor: "sobrenome" },
    { header: "Email", accessor: "email" },
    { header: "Cpf", accessor: "cpf" },
    { header: "Telefone", accessor: "telefone" },
    { header: "Categoria", accessor: "categoria" },
    { header: "Confirmação", accessor: "confirmacao" },

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
          `/convidado?email_convidado=${dadosForm.email}`,
          dados,
        );

        if (res.status === 200) {
          toast.success(res.data.mensagem);
          await buscarConvidados();
          setShowModal(false);
        }
      } else if (!editando) {
        const res = await Api.post("/convidado", dados);
        if (res.status === 201) {
          toast.success(res.data.mensagem);
          await buscarConvidados();
          setShowModal(false);
        }
      }
    } catch (err) {
      const erros = err.response?.data?.erros;

      if (erros) {
        Object.values(erros).forEach((msg) => {
          toast.error(msg);
        });
      } else {
        toast.error(err.response?.data.mensagem || "Erro ao enviar dados");
      }
    }
  };

  const deletarConvidado = async () => {

    try {
      const res = await Api.delete(
        `/convidado?email_convidado=${dadosForm.email}`,
      );
      if (res.status === 200) {
        toast.success(res.data.mensagem);
        await buscarConvidados();
        setShowModalDeletar(false);
      }
    } catch (err) {
      toast.error(err.response.data.mensagem || "Erro ao deletar convidado");
    }
  };

  return (
    <>
      <h1>Convidados</h1>

      <Button
        onClick={() => {
          setShowModal(true);
          setDadosForm(null);
        }}
        className="my-3 ignorar-fonte-btn"
        variant="primary"
      >
        <IoMdAddCircleOutline /> Criar novo
      </Button>
      <DadosTable
        columns={columns}
        rows={convidados}
        keyField={"id_convidado"}
      />
      <ConvidadoModal
        dados={dadosForm}
        handleClose={() => setShowModal(false)}
        show={showModal}
        onSubmit={enviarDadosForm}
      />

      <ModalDeletar
        deletar={deletarConvidado}
        show={showModalDeletar}
        handleClose={() => setShowModalDeletar(false)}
      />
    </>
  );
}

export default Convidados;
