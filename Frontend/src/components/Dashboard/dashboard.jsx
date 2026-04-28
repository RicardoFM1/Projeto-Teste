import { useEffect, useState } from "react";
import DashboardCard from "../Cards/Dashboard/dashboardCard";
import DataTable from "../Table/datatable";
import Api from "../../API/api";

function Dashboard() {
  const [usuarios, setUsuarios] = useState([]);

  const buscarUsuarios = async () => {
    try {
      const res = await Api.get("/usuario");
  
      setUsuarios(res.data.dados);
      console.log(usuarios)
    } catch (err) {
      console.log(err);
    }
  };

  useEffect(() => {
    
    buscarUsuarios();
    
  }, []);

  const columns = [
    { header: 'Nome', accessor: 'nome' },
    { header: 'Email', accessor: 'email' },
    { header: 'Cpf', accessor: 'cpf' },
    { header: 'Cargo', accessor: 'cargo' },
  ];

  return (
    <>
      <h1>Dashboard</h1>
      <DashboardCard />
      <DataTable columns={columns} rows={usuarios}/>
    </>
  );
}

export default Dashboard;
