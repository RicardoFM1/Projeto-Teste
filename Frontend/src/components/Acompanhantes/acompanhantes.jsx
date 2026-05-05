import { useEffect, useState } from "react";
import Api from "../../API/api";
import { Button } from "react-bootstrap";
import { CiEdit } from "react-icons/ci";
import { MdDelete } from "react-icons/md";
import { IoMdAddCircleOutline } from "react-icons/io";
import DadosTable from "../Table/table";
import { toast } from "react-toastify";
import AcompanhanteModal from "../Modais/Acompanhante/modalAcompanhante";
import ModalDeletar from "../Modais/Deletar/modalDeletar";

function Acompanhantes() {
  const [acompanhantes, setAcompanhantes] = useState([]);
  const [convidados, setConvidados] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [showModalDeletar, setShowModalDeletar] = useState(false);
  const [dadosForm, setDadosForm] = useState(null);

  const buscarAcompanhantes = async () => {
    try {
      const res = await Api.get("/acompanhante");
      setAcompanhantes(res.data.dados);
    } catch (err) {
      toast.error("Erro ao buscar acompanhantes");
      console.log(err);
    }
  };

  const buscarConvidados = async () => {
    try {
      const res = await Api.get("/convidado");
      setConvidados(res.data.dados);
    } catch (err) {
      toast.error("Erro ao buscar convidados");
      console.log(err);
    }
  };

  useEffect(() => {
    buscarAcompanhantes();
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
    { header: "Id", accessor: "id_acompanhante" },
    { header: "Nome", accessor: "nome" },
    { header: "Sobrenome", accessor: "sobrenome" },
    { header: "CPF", accessor: "cpf" },
    { header: "Idade", accessor: "idade" },
    { header: "CPF(convidado)", accessor: "convidado_cpf" },
    {
      header: "Ações",
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
        const res = await Api.put(`/acompanhante?id_acompanhante=${dadosForm.id_acompanhante}`, dados);
        if (res.status === 200) {
          toast.success(res.data.mensagem);
          await buscarAcompanhantes();
          setShowModal(false);
        }
      } else {
        const res = await Api.post("/acompanhante", dados);
        if (res.status === 201) {
          toast.success(res.data.mensagem);
          await buscarAcompanhantes();
          setShowModal(false);
        }
      }
    } catch (err) {
      toast.error(err.response?.data.mensagem || "Erro ao enviar dados");
    }
  };

  const deletarAcompanhante = async () => {
    try {
      const res = await Api.delete(`/acompanhante?id_acompanhante=${dadosForm.id_acompanhante}`);
      if (res.status === 200) {
        toast.success(res.data.mensagem);
        await buscarAcompanhantes();
        setShowModalDeletar(false);
      }
    } catch (err) {
      toast.error(err.response?.data.mensagem || "Erro ao deletar acompanhante");
    }
  };

  return (
    <>
      <h1>Acompanhantes</h1>
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
      <DadosTable columns={columns} rows={acompanhantes} keyField={"id_acompanhante"} />
      <AcompanhanteModal
        convidados={convidados}
        dados={dadosForm}
        handleClose={() => setShowModal(false)}
        show={showModal}
        onSubmit={enviarDadosForm}
      />
      <ModalDeletar
        deletar={deletarAcompanhante}
        show={showModalDeletar}
        handleClose={() => setShowModalDeletar(false)}
      />
    </>
  );
}

export default Acompanhantes;
