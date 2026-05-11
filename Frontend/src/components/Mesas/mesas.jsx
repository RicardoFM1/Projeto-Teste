import { useEffect, useState } from "react";
import Api from "../../API/api";
import { Button, Card } from "react-bootstrap";
import { CiEdit } from "react-icons/ci";
import { MdDelete } from "react-icons/md";
import { IoMdAddCircleOutline } from "react-icons/io";
import DadosTable from "../Table/table";
import { toast } from "react-toastify";
import MesaModal from "../Modais/Mesa/modalMesa";
import ModalDeletar from "../Modais/Deletar/modalDeletar";
import style from "./mesas.module.css"

function Mesas() {
  const [mesas, setMesas] = useState([]);
  const [showModal, setShowModal] = useState(false);
  const [showModalDeletar, setShowModalDeletar] = useState(false);
  const [dadosForm, setDadosForm] = useState(null);

  const buscarMesas = async () => {
    try {
      const res = await Api.get("/mesa");
      setMesas(res.data);
    } catch (err) {
      toast.error("Erro ao buscar mesas");
      console.log(err);
    }
  };

  useEffect(() => {
    buscarMesas();
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
    { header: "Id", accessor: "id_mesa" },
    { header: "Capacidade", accessor: "capacidade" },
    { header: "Restrição", accessor: "restricao" },
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
        const res = await Api.put(`/mesa?id_mesa=${dadosForm.id_mesa}`, dados);
        if (res.status === 200) {
          toast.success(res.data.mensagem);
          await buscarMesas();
          setShowModal(false);
        }
      } else {
        const res = await Api.post("/mesa", dados);
        if (res.status === 201) {
          toast.success(res.data.mensagem);
          await buscarMesas();
          setShowModal(false);
        }
      }
    } catch (err) {
      toast.error(err.response?.data.mensagem || "Erro ao enviar dados");
    }
  };

  const deletarMesa = async () => {
    try {
      const res = await Api.delete(`/mesa?id_mesa=${dadosForm.id_mesa}`);
      if (res.status === 200) {
        toast.success(res.data.mensagem);
        await buscarMesas();
        setShowModalDeletar(false);
      }
    } catch (err) {
      toast.error(err.response?.data.mensagem || "Erro ao deletar mesa");
    }
  };

  return (
    <>
      <h1>Mesas</h1>
      <Card style={{ maxWidth: "20%" }} className="mb-3">
        <Card.Body className={style.cardBody}>
          Total de mesas:
          <span className={style.cardSpan}>
            {" "}
            <strong>{mesas?.total}</strong>{" "}
          </span>
        </Card.Body>
      </Card>
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
      <DadosTable columns={columns} rows={mesas?.dados} keyField={"id_mesa"} />
      <MesaModal
        dados={dadosForm}
        handleClose={() => setShowModal(false)}
        show={showModal}
        onSubmit={enviarDadosForm}
      />
      <ModalDeletar
        deletar={deletarMesa}
        show={showModalDeletar}
        handleClose={() => setShowModalDeletar(false)}
      />
    </>
  );
}

export default Mesas;