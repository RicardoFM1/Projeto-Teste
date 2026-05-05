import { use, useEffect, useState } from "react";
import Api from "../../API/api";
import { Button } from "react-bootstrap";
import { CiEdit } from "react-icons/ci";
import { MdDelete } from "react-icons/md";
import { IoMdAddCircleOutline } from "react-icons/io";
import DadosTable from "../Table/table";
import { toast } from "react-toastify";
import CheckinModal from "../Modais/Checkin/modalCheckin";



function Checkin() {
    const [checkins, setCheckins] = useState([]);
    const [convidados, setConvidados] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [dadosForm, setDadosForm] = useState(null);

    const buscarCheckins = async () => {
        try {
            const res = await Api.get("/checkin");

            setCheckins(res.data.dados);
            console.log(res.data.dados);
        } catch (err) {
            toast.error('Erro ao buscar checkins');
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
        buscarCheckins();
        buscarConvidados();
    }, []);


    const columns = [
        { header: "Checkin Id", accessor: "id_checkin" },
        { header: "CPF(Usuario)", accessor: "usuario_cpf" },
        { header: "CPF(Convidado)", accessor: "convidado_cpf" },
        { header: "Data e hora", accessor: "data_e_hora" }
    ];

    const enviarDados = async (dados) => {
        try {

            const res = await Api.post('/checkin', dados)
            if (res.status === 201) {
                toast.success(res.data.mensagem);
                await buscarCheckins()
                setShowModal(false)
            }

        } catch (err) {
            toast.error(err.response.data.mensagem);
            
        }
    }


    return (
        <>
            <h1>Checkins</h1>
            <Button onClick={() => setShowModal(true)} className="my-3 ignorar-fonte-btn" variant="primary">
                <IoMdAddCircleOutline /> Criar novo
            </Button>
            <DadosTable columns={columns} rows={checkins} keyField={"id_checkin"} />
            <CheckinModal convidados={convidados} handleClose={() => setShowModal(false)} show={showModal} onSubmit={enviarDados} />
           
            
        </>
    );
}

export default Checkin;